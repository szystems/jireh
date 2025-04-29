<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MetaVenta extends Model
{
    use HasFactory;

    protected $table = 'metas_ventas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usuario_id',
        'monto_minimo',
        'monto_maximo',
        'porcentaje_comision',
        'periodo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'observaciones',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'monto_minimo' => 'decimal:2',
        'monto_maximo' => 'decimal:2',
        'porcentaje_comision' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'estado' => 'boolean',
    ];

    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verificar si se cumple la meta
     *
     * @param float $montoVentas El monto total de ventas del usuario
     * @return bool
     */
    public function esCumplida($montoVentas)
    {
        return $montoVentas >= $this->monto_minimo &&
               ($this->monto_maximo === null || $montoVentas <= $this->monto_maximo);
    }

    /**
     * Calcular comisión basada en el monto de ventas
     *
     * @param float $montoVentas El monto total de ventas del usuario
     * @return float
     */
    public function calcularComision($montoVentas)
    {
        if ($this->esCumplida($montoVentas)) {
            return $montoVentas * ($this->porcentaje_comision / 100);
        }
        return 0;
    }

    /**
     * Scope para metas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para metas del periodo actual
     */
    public function scopePeriodoActual($query)
    {
        $hoy = Carbon::now();
        return $query->where('fecha_inicio', '<=', $hoy)
                    ->where(function($q) use ($hoy) {
                        $q->where('fecha_fin', '>=', $hoy)
                          ->orWhereNull('fecha_fin');
                    });
    }

    /**
     * Scope para metas de un usuario específico
     */
    public function scopeDeUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Retorna las metas vigentes para un usuario
     */
    public static function metasVigentes($usuarioId)
    {
        return self::activas()
                    ->periodoActual()
                    ->deUsuario($usuarioId)
                    ->get();
    }
}
