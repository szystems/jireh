<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserPasswordManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => false,
            'mail.default' => 'array',
        ]);

        \Illuminate\Support\Facades\DB::purge('sqlite');
        \Illuminate\Support\Facades\DB::reconnect('sqlite');
        \Illuminate\Support\Facades\DB::setDefaultConnection('sqlite');

        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->createUsersTable();
    }

    public function test_usuario_cambia_su_contrasena_desde_el_perfil()
    {
        $user = $this->makeUser(['password' => Hash::make('antigua123')]);

        $response = $this->actingAs($user)->post('update-user/'.$user->id, $this->profilePayload($user, [
            'current_password' => 'antigua123',
            'password' => 'nuevaClave9',
            'password_confirmation' => 'nuevaClave9',
        ]));

        $response->assertRedirect('show-user/'.$user->id);
        $this->assertTrue(Hash::check('nuevaClave9', $user->fresh()->password));
    }

    public function test_rechaza_cambio_si_la_contrasena_actual_es_incorrecta()
    {
        $user = $this->makeUser(['password' => Hash::make('antigua123')]);

        $response = $this->actingAs($user)->from('edit-user/'.$user->id)->post('update-user/'.$user->id, $this->profilePayload($user, [
            'current_password' => 'no-es-esa',
            'password' => 'nuevaClave9',
            'password_confirmation' => 'nuevaClave9',
        ]));

        $response->assertRedirect('edit-user/'.$user->id);
        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('antigua123', $user->fresh()->password));
    }

    public function test_no_altera_la_contrasena_si_los_campos_van_vacios()
    {
        $hash = Hash::make('se-queda');
        $user = $this->makeUser(['password' => $hash]);

        $this->actingAs($user)->post('update-user/'.$user->id, $this->profilePayload($user));

        $this->assertSame($hash, $user->fresh()->password);
    }

    public function test_admin_puede_asignar_contrasena_a_otro_usuario()
    {
        $admin = $this->makeUser(['role_as' => 0, 'email' => 'admin@jireh.test']);
        $vendedor = $this->makeUser([
            'role_as' => 1,
            'email' => 'vendedor@jireh.test',
            'password' => Hash::make('vieja1234'),
        ]);

        $this->actingAs($admin)->post('update-user/'.$vendedor->id, $this->profilePayload($vendedor, [
            'password' => 'asignada88',
            'password_confirmation' => 'asignada88',
        ]));

        $this->assertTrue(Hash::check('asignada88', $vendedor->fresh()->password));
    }

    public function test_vendedor_no_puede_cambiar_contrasena_de_otro_usuario()
    {
        $actor = $this->makeUser(['role_as' => 1, 'email' => 'yo@jireh.test']);
        $otro = $this->makeUser([
            'role_as' => 1,
            'email' => 'otro@jireh.test',
            'password' => Hash::make('intacta12'),
        ]);

        $response = $this->actingAs($actor)->from('edit-user/'.$otro->id)->post('update-user/'.$otro->id, $this->profilePayload($otro, [
            'password' => 'hackeada1',
            'password_confirmation' => 'hackeada1',
        ]));

        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('intacta12', $otro->fresh()->password));
    }

    public function test_al_crear_usuario_la_contrasena_queda_hasheada()
    {
        $admin = $this->makeUser(['role_as' => 0, 'email' => 'admin2@jireh.test']);

        $this->actingAs($admin)->post('insert-user', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@jireh.test',
            'fecha_nacimiento' => '1990-01-15',
            'password' => 'inicial99',
            'password_confirmation' => 'inicial99',
            'role_as' => 1,
        ]);

        $created = User::where('email', 'nuevo@jireh.test')->first();
        $this->assertNotNull($created);
        $this->assertTrue(Hash::check('inicial99', $created->password));
        $this->assertNotSame('inicial99', $created->password);
    }

    public function test_reset_por_correo_devuelve_error_usable_si_smtp_falla()
    {
        $broker = \Mockery::mock(\Illuminate\Contracts\Auth\PasswordBroker::class);
        $broker->shouldReceive('sendResetLink')->once()->andThrow(new \RuntimeException('Connection refused'));
        Password::shouldReceive('broker')->andReturn($broker);

        $response = $this->from(route('password.request'))->post(route('password.email'), [
            'email' => 'cliente@jireh.test',
        ]);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'No se pudo enviar el correo',
            session('errors')->first('email')
        );
    }

    private function profilePayload(User $user, array $extra = []): array
    {
        return array_merge([
            'name' => $user->name,
            'email' => $user->email,
            'fecha_nacimiento' => '1990-05-20',
            'telefono' => '12345678',
            'celular' => '87654321',
            'direccion' => 'Ciudad',
        ], $extra);
    }

    private function makeUser(array $overrides = []): User
    {
        return User::unguarded(function () use ($overrides) {
            return User::create(array_merge([
                'name' => 'Usuario Prueba',
                'email' => 'user'.uniqid().'@jireh.test',
                'password' => Hash::make('password'),
                'role_as' => 0,
                'estado' => 1,
                'principal' => 0,
                'fecha_nacimiento' => '1990-05-20',
            ], $overrides));
        });
    }

    private function createUsersTable(): void
    {
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('role_as')->default(0);
            $table->tinyInteger('principal')->default(0);
            $table->tinyInteger('estado')->default(1);
            $table->string('fotografia')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('celular')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
}
