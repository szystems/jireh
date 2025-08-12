<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'vehiculo_id',
        'numero_factura',
        'fecha',
        'tipo_venta',
        'usuario_id',
        'estado',
        'estado_pago'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fecha' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Relación con el usuario (vendedor) que realizó la venta.
     * Esta relación usa usuario_id que está en fillable.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    /**
     * Accessor para calcular el total de la venta
     */
    public function getTotalAttribute()
    {
        return $this->detalleVentas->sum('sub_total');
    }

    /**
     * Obtiene los pagos asociados a esta venta.
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    /**
     * Alias de usuario() para compatibilidad.
     * Esta relación usa el mismo campo que usuario().
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Alias adicional para compatibilidad si se usa 'vendedor'.
     * Esta relación usa el mismo campo que usuario().
     */
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación con comisiones directas de la venta
     */
    public function comisiones()
    {
        return $this->hasMany(Comision::class);
    }

    /**
     * Genera todas las comisiones para esta venta
     */
    public function generarComisiones()
    {
        // Generar comisiones para mecánicos y lavadores
        foreach ($this->detalleVentas as $detalle) {
            // Comisión para mecánico si aplica
            $detalle->generarComisionMecanico();

            // Comisiones para lavadores si hay asignados
            $detalle->generarComisionesCarwash();
        }

        // Comisión para el vendedor basada en metas
        $this->generarComisionVendedor();

        return true;
    }

    /**
     * Genera la comisión para el vendedor según sus metas
     * Ahora evalúa cada meta según su tipo específico (mensual, semestral, anual)
     */
    public function generarComisionVendedor()
    {
        // Solo si hay un vendedor asignado
        if (!$this->usuario_id) return null;

        $fechaActual = Carbon::now();
        $usuario = $this->usuario;
        if (!$usuario) return null;

        // Obtener todas las metas activas para evaluar
        $todasLasMetas = MetaVenta::where('estado', 1)->orderBy('monto_minimo')->get();
        
        $comisionesCreadas = collect();
        
        // Evaluar cada meta según su tipo específico
        foreach ($todasLasMetas as $meta) {
            // Calcular ventas según el tipo de meta específico
            $ventasParaMeta = $this->calcularVentasSegunTipoMeta($usuario, $meta, $fechaActual);
            
            // Verificar si alcanza esta meta
            if ($ventasParaMeta >= $meta->monto_minimo) {
                // Determinar el período de la meta para verificar duplicados
                $fechasPeriodo = $this->obtenerFechasPorTipoMeta($meta, $fechaActual);
                
                // Verificar si ya existe una comisión para esta meta en este período
                $comisionExistente = Comision::where([
                    'commissionable_id' => $this->usuario_id,
                    'commissionable_type' => 'App\Models\User',
                    'tipo_comision' => 'meta_venta'
                ])
                ->whereBetween('fecha_calculo', [$fechasPeriodo['inicio'], $fechasPeriodo['fin']])
                ->first();

                // Calcular comisión según el porcentaje de la meta
                $montoComision = $meta->calcularComision($ventasParaMeta);
                
                if ($comisionExistente && $montoComision > 0) {
                    // Actualizar comisión existente
                    $comisionExistente->update([
                        'monto' => $montoComision,
                        'porcentaje' => $meta->porcentaje_comision,
                        'observaciones' => "Comisión por meta {$meta->nombre}: Q{$meta->monto_minimo}" . 
                                         ($meta->monto_maximo ? " - Q{$meta->monto_maximo}" : " - Sin límite") .
                                         " | Total vendido: Q" . number_format($ventasParaMeta, 2)
                    ]);
                    
                    $comisionesCreadas->push($comisionExistente);
                    
                } elseif (!$comisionExistente && $montoComision > 0) {
                    // Crear nueva comisión
                    $nuevaComision = Comision::create([
                        'commissionable_id' => $this->usuario_id,
                        'commissionable_type' => 'App\Models\User',
                        'tipo_comision' => 'meta_venta',
                        'monto' => $montoComision,
                        'porcentaje' => $meta->porcentaje_comision,
                        'venta_id' => $this->id,
                        'fecha_calculo' => now(),
                        'estado' => 'pendiente',
                        'observaciones' => "Comisión por meta {$meta->nombre}: Q{$meta->monto_minimo}" . 
                                         ($meta->monto_maximo ? " - Q{$meta->monto_maximo}" : " - Sin límite") .
                                         " | Total vendido: Q" . number_format($ventasParaMeta, 2)
                    ]);
                    
                    $comisionesCreadas->push($nuevaComision);
                }
            }
        }
        
        return $comisionesCreadas;
    }

    /**
     * Calcular ventas de un trabajador según el tipo específico de meta
     * (Reutiliza la lógica del ReporteMetasController)
     */
    private function calcularVentasSegunTipoMeta($usuario, $meta, $fechaActual)
    {
        $fechasPeriodo = $this->obtenerFechasPorTipoMeta($meta, $fechaActual);
        
        // Calcular ventas del período específico de esta meta
        $ventas = $usuario->ventas()
            ->whereBetween('fecha', [$fechasPeriodo['inicio'], $fechasPeriodo['fin']])
            ->where('estado', 1)
            ->with('detalleVentas')
            ->get();
            
        return $ventas->sum(function($venta) {
            return $venta->detalleVentas->sum('sub_total');
        });
    }

    /**
     * Obtener fechas de inicio y fin según el tipo de meta
     */
    private function obtenerFechasPorTipoMeta($meta, $fechaActual)
    {
        // Determinar el período según el tipo de meta
        $tipoMeta = strtolower($meta->nombre);
        
        if (strpos($tipoMeta, 'mensual') !== false || strpos($tipoMeta, 'mes') !== false) {
            // Meta mensual: ventas del mes actual
            return [
                'inicio' => $fechaActual->copy()->startOfMonth(),
                'fin' => $fechaActual->copy()->endOfMonth()
            ];
        } elseif (strpos($tipoMeta, 'semestral') !== false || strpos($tipoMeta, 'semestre') !== false) {
            // Meta semestral: ventas del semestre actual
            $mes = $fechaActual->month;
            if ($mes <= 6) {
                return [
                    'inicio' => $fechaActual->copy()->startOfYear(),
                    'fin' => $fechaActual->copy()->month(6)->endOfMonth()
                ];
            } else {
                return [
                    'inicio' => $fechaActual->copy()->month(7)->startOfMonth(),
                    'fin' => $fechaActual->copy()->endOfYear()
                ];
            }
        } elseif (strpos($tipoMeta, 'anual') !== false || strpos($tipoMeta, 'año') !== false) {
            // Meta anual: ventas del año actual
            return [
                'inicio' => $fechaActual->copy()->startOfYear(),
                'fin' => $fechaActual->copy()->endOfYear()
            ];
        } else {
            // Por defecto, usar el período mensual
            return [
                'inicio' => $fechaActual->copy()->startOfMonth(),
                'fin' => $fechaActual->copy()->endOfMonth()
            ];
        }
    }
}
