<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Comision extends Model
{
    use HasFactory;

    protected $table = 'comisiones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */    protected $fillable = [
        'commissionable_id',
        'commissionable_type',
        'tipo_comision',
        'monto',
        'porcentaje',
        'detalle_venta_id',
        'venta_id',
        'articulo_id',
        'fecha_calculo',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'monto' => 'decimal:2',
        'porcentaje' => 'decimal:2',
        'fecha_calculo' => 'date',
    ];

    /**
     * Obtener el modelo relacionado con esta comisión (trabajador o usuario)
     */
    public function commissionable()
    {
        return $this->morphTo();
    }

    /**
     * Relación específica con trabajador (para facilitar el acceso)
     */
    public function trabajador()
    {
        return $this->morphTo('commissionable')->where('commissionable_type', 'App\Models\Trabajador');
    }

    /**
     * Relación con el detalle de venta asociado
     */
    public function detalleVenta()
    {
        return $this->belongsTo(DetalleVenta::class);
    }

    /**
     * Relación con la venta asociada
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Relación con el artículo asociado
     */
    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    /**
     * Relación con los pagos de comisión
     */
    public function pagos()
    {
        return $this->hasMany(PagoComision::class);
    }

    /**
     * Calcula el monto total pagado de la comisión
     */
    public function montoPagado()
    {
        return $this->pagos()->where('estado', 'completado')->sum('monto');
    }

    /**
     * Calcula el monto pendiente por pagar
     */
    public function montoPendiente()
    {
        return $this->monto - $this->montoPagado();
    }

    /**
     * Verifica si la comisión está completamente pagada
     */
    public function estaPagada()
    {
        return $this->estado === 'pagado' || $this->montoPendiente() <= 0;
    }

    /**
     * Actualiza automáticamente el estado basado en los pagos
     */
    public function actualizarEstado()
    {
        if ($this->montoPendiente() <= 0) {
            $this->estado = 'pagado';
        } else {
            // Si tiene pagos pero no está completamente pagada, mantener como pendiente
            $this->estado = 'pendiente';
        }
        return $this->save();
    }

    /**
     * Scope para obtener solo comisiones pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para obtener comisiones del periodo actual (mes en curso)
     */
    public function scopeDelMesActual($query)
    {
        $inicio = Carbon::now()->startOfMonth();
        $fin = Carbon::now()->endOfMonth();
        return $query->whereBetween('fecha_calculo', [$inicio, $fin]);
    }

    /**
     * Scope para filtrar por tipo de receptor (usuario o trabajador)
     */
    public function scopePorTipoReceptor($query, $tipo)
    {
        if ($tipo === 'usuario') {
            return $query->where('commissionable_type', 'App\Models\User');
        } else if ($tipo === 'trabajador') {
            return $query->where('commissionable_type', 'App\Models\Trabajador');
        }
        return $query;
    }

    /**
     * Obtener las ventas que conformaron esta comisión de meta_venta
     */
    public function getVentasDeMetaAttribute()
    {
        if ($this->tipo_comision !== 'meta_venta' || $this->commissionable_type !== 'App\Models\User') {
            return collect();
        }

        // Calcular el período de la meta basado en fecha_calculo
        $fechaCalculo = Carbon::parse($this->fecha_calculo);
        $fechaInicio = $fechaCalculo->copy()->startOfMonth();
        $fechaFin = $fechaCalculo->copy()->endOfMonth();

        // Obtener las ventas del vendedor en ese período
        $ventas = \App\Models\Venta::with(['cliente', 'detalleVentas'])
            ->where('usuario_id', $this->commissionable_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 1)
            ->orderBy('fecha', 'desc')
            ->get();

        return $ventas;
    }

    /**
     * Obtener información resumida de la meta
     */
    public function getInfoMetaResumenAttribute()
    {
        if ($this->tipo_comision !== 'meta_venta') {
            return null;
        }

        $ventas = $this->ventas_de_meta;
        
        if ($ventas->isEmpty()) {
            return [
                'periodo' => $this->fecha_calculo->format('m/Y'),
                'cantidad_ventas' => 0,
                'total_vendido' => 0,
                'meta_info' => $this->obtenerInfoMeta()
            ];
        }

        return [
            'periodo' => $this->fecha_calculo->format('m/Y'),
            'fecha_inicio' => $ventas->min('fecha')->format('d/m/Y'),
            'fecha_fin' => $ventas->max('fecha')->format('d/m/Y'),
            'cantidad_ventas' => $ventas->count(),
            'total_vendido' => $ventas->sum(function($venta) {
                return $venta->detalleVentas->sum('sub_total');
            }),
            'meta_info' => $this->obtenerInfoMeta()
        ];
    }

    /**
     * Obtener información de la meta alcanzada (método auxiliar)
     */
    private function obtenerInfoMeta()
    {
        $porcentaje = $this->porcentaje;
        
        switch($porcentaje) {
            case 3:
                return [
                    'nombre' => 'Bronce',
                    'color' => 'warning',
                    'rango' => '$1K - $2.5K'
                ];
            case 5:
                return [
                    'nombre' => 'Plata', 
                    'color' => 'secondary',
                    'rango' => '$2.5K - $5K'
                ];
            case 8:
                return [
                    'nombre' => 'Oro',
                    'color' => 'success', 
                    'rango' => '$5K+'
                ];
            default:
                return [
                    'nombre' => 'Desconocida',
                    'color' => 'dark',
                    'rango' => 'N/A'
                ];
        }
    }
}
