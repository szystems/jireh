<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TrabajadorDetalleVenta extends Model
{
    use HasFactory;

    // Esta es una tabla pivot con un modelo personalizado
    protected $table = 'trabajador_detalle_venta';

    protected $fillable = [
        'trabajador_id',
        'detalle_venta_id',
        'monto_comision',
    ];

    protected $casts = [
        'monto_comision' => 'decimal:2',
    ];

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function detalleVenta()
    {
        return $this->belongsTo(DetalleVenta::class);
    }

    /**
     * Genera una comisión para el trabajador
     */
    public function generarComision()
    {
        // Verificar si ya existe una comisión para esta asignación
        $comisionExistente = Comision::where([
            'commissionable_id' => $this->trabajador_id,
            'commissionable_type' => 'App\Models\Trabajador',
            'detalle_venta_id' => $this->detalle_venta_id,
        ])->first();

        if (!$comisionExistente && $this->monto_comision > 0) {
            // Obtener el detalle de venta si no está cargado
            $detalleVenta = $this->detalleVenta ?? DetalleVenta::find($this->detalle_venta_id);
            
            if (!$detalleVenta) {
                Log::error("No se pudo encontrar el detalle de venta con ID: {$this->detalle_venta_id}");
                return null;
            }

            // Crear la comisión
            return Comision::create([
                'commissionable_id' => $this->trabajador_id,
                'commissionable_type' => 'App\Models\Trabajador',
                'tipo_comision' => 'carwash',
                'monto' => $this->monto_comision,
                'detalle_venta_id' => $this->detalle_venta_id,
                'venta_id' => $detalleVenta->venta_id,
                'articulo_id' => $detalleVenta->articulo_id,
                'fecha_calculo' => now(),
                'estado' => 'pendiente',
            ]);
        }

        return $comisionExistente;
    }
}
