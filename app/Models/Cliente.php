<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $primaryKey='id';

    protected $fillable = [
        'nombre',
        'fecha_nacimiento',
        'telefono',
        'celular',
        'direccion',
        'email',
        'dpi',
        'nit',
        'fotografia',
        'estado',
    ];

    /**
     * Los atributos que deben ser ocultados para las matrices.
     *
     * @var array
     */
    protected $hidden = [
        // Aquí puedes añadir los atributos que no quieres que se muestren
        // por ejemplo 'fotografia', si no deseas exponer la URL en las respuestas JSON
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'estado' => 'boolean',
        // Aquí puedes añadir otros casts si es necesario, por ejemplo:
        // 'email_verified_at' => 'datetime',
    ];

    /**
     * Obtiene los vehículos asociados a este cliente.
     */
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }
}
