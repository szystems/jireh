<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_as',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relación polimórfica para comisiones
     */
    public function comisiones()
    {
        return $this->morphMany(Comision::class, 'commissionable');
    }

    /**
     * Obtiene las comisiones pendientes por pagar
     */
    public function comisionesPendientes()
    {
        return $this->comisiones()->where('estado', 'pendiente');
    }

    /**
     * Calcula el total de comisiones pendientes
     */
    public function totalComisionesPendientes()
    {
        return $this->comisionesPendientes()->sum('monto');
    }

    /**
     * Obtiene las comisiones pagadas
     */
    public function comisionesPagadas()
    {
        return $this->comisiones()->where('estado', 'pagado');
    }

    /**
     * Calcula el total de comisiones pagadas
     */
    public function totalComisionesPagadas()
    {
        return $this->comisionesPagadas()->sum('monto');
    }

    /**
     * Relación con las metas de ventas
     */
    public function metasVentas()
    {
        return $this->hasMany(MetaVenta::class, 'usuario_id');
    }

    /**
     * Obtiene las metas actuales del usuario
     */
    public function metasActuales()
    {
        return $this->metasVentas()
                   ->where('estado', true)
                   ->where('fecha_inicio', '<=', now())
                   ->where(function($query) {
                       $query->where('fecha_fin', '>=', now())
                             ->orWhereNull('fecha_fin');
                   });
    }

    /**
     * Relación con ventas
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'usuario_id');
    }
}
