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
     */    protected $fillable = [
        'nombre',
        'descripcion',
        'monto_minimo',
        'monto_maximo',
        'porcentaje_comision',
        'periodo',
        'estado'
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
        'estado' => 'boolean'
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
     * NOTA: Las metas son generales, no tienen fechas específicas
     */
    public function scopePeriodoActual($query)
    {
        // Para metas generales, simplemente retornamos todas las activas
        return $query->where('estado', true);
    }

    /**
     * Scope para metas de un usuario específico
     * NOTA: Las metas son generales, no por usuario específico
     */
    public function scopeDeUsuario($query, $usuarioId)
    {
        // Para metas generales, ignoramos el usuario y retornamos todas las activas
        return $query->where('estado', true);
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

    /**
     * Determinar qué meta corresponde a un monto vendido
     */
    public static function determinarMetaPorMonto($montoVendido, $periodo = 'mensual')
    {
        return self::activas()
            ->where('periodo', $periodo)
            ->where('monto_minimo', '<=', $montoVendido)
            ->where(function($query) use ($montoVendido) {
                $query->whereNull('monto_maximo')
                      ->orWhere('monto_maximo', '>=', $montoVendido);
            })
            ->orderBy('monto_minimo', 'desc')
            ->first();
    }

    /**
     * Obtener todas las metas ordenadas por monto mínimo
     */
    public static function obtenerMetasOrdenadas($periodo = 'mensual')
    {
        return self::activas()
            ->where('periodo', $periodo)
            ->orderBy('monto_minimo', 'asc')
            ->get();
    }

    /**
     * Formatear el rango de montos para mostrar
     */
    public function getRangoFormateadoAttribute()
    {
        $inicio = 'Q' . number_format($this->monto_minimo, 2);
        
        if ($this->monto_maximo) {
            $fin = 'Q' . number_format($this->monto_maximo, 2);
            return "{$inicio} - {$fin}";
        }
        
        return "{$inicio} en adelante";
    }

    /**
     * Formatear el porcentaje de comisión
     */
    public function getPorcentajeFormateadoAttribute()
    {
        return $this->porcentaje_comision . '%';
    }
}
