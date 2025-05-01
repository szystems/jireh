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
     */
    public function generarComisionVendedor()
    {
        // Solo si hay un vendedor asignado
        if (!$this->usuario_id) return null;

        // Obtener metas vigentes del usuario
        $metas = MetaVenta::activas()
                 ->periodoActual()
                 ->deUsuario($this->usuario_id)
                 ->get();

        if ($metas->isEmpty()) return null;

        // Calcular el monto total de ventas del usuario para el periodo actual
        $inicio = Carbon::now()->startOfMonth();
        $fin = Carbon::now()->endOfMonth();

        // Obtener total de ventas del vendedor en el periodo
        $totalVentas = Venta::where('usuario_id', $this->usuario_id)
                      ->whereBetween('fecha', [$inicio, $fin])
                      ->where('estado', true)
                      ->with('detalleVentas')
                      ->get()
                      ->sum(function($venta) {
                          return $venta->detalleVentas->sum('sub_total');
                      });

        // Verificar cada meta y crear comisión si corresponde
        foreach ($metas as $meta) {
            if ($meta->esCumplida($totalVentas)) {
                // Calcular comisión según el porcentaje de la meta
                $montoComision = $meta->calcularComision($totalVentas);

                // Verificar si ya existe una comisión para esta venta y usuario
                $comisionExistente = Comision::where([
                    'commissionable_id' => $this->usuario_id,
                    'commissionable_type' => 'App\Models\User',
                    'venta_id' => $this->id,
                    'tipo_comision' => 'meta_venta'
                ])->first();

                if (!$comisionExistente && $montoComision > 0) {
                    // Crear la comisión
                    return Comision::create([
                        'commissionable_id' => $this->usuario_id,
                        'commissionable_type' => 'App\Models\User',
                        'tipo_comision' => 'meta_venta',
                        'monto' => $montoComision,
                        'porcentaje' => $meta->porcentaje_comision,
                        'venta_id' => $this->id,
                        'fecha_calculo' => now(),
                        'estado' => 'pendiente',
                        'observaciones' => "Comisión por meta de ventas: {$meta->monto_minimo} - {$meta->monto_maximo}"
                    ]);
                }

                return $comisionExistente;
            }
        }

        return null;
    }
}
