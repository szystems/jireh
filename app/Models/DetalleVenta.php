<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'articulo_id',
        'cantidad',
        'precio_costo',
        'precio_venta',
        'descuento_id',
        'trabajador_id',
        'usuario_id',
        'sub_total',
        'porcentaje_impuestos',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function descuento()
    {
        return $this->belongsTo(Descuento::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function tipoComisionTrabajador()
    {
        return $this->belongsTo(TipoComision::class, 'tipo_comision_trabajador_id');
    }

    public function tipoComisionUsuario()
    {
        return $this->belongsTo(TipoComision::class, 'tipo_comision_usuario_id');
    }

    /**
     * Relación muchos a muchos con trabajadores de car wash
     */
    public function trabajadoresCarwash()
    {
        return $this->belongsToMany(Trabajador::class, 'trabajador_detalle_venta')
                    ->withPivot('monto_comision')
                    ->withTimestamps();
    }

    /**
     * Relación con comisiones
     */
    public function comisiones()
    {
        return $this->hasMany(Comision::class);
    }

    /**
     * Genera comisiones para mecánicos asociados al artículo
     */
    public function generarComisionMecanico()
    {
        // Solo si el artículo es un servicio que tiene mecánico asignado
        if ($this->articulo &&
            $this->articulo->tipo === 'servicio' &&
            $this->articulo->mecanico_id &&
            $this->articulo->costo_mecanico > 0) {

            // Verificar si ya existe la comisión
            $comisionExistente = Comision::where([
                'commissionable_id' => $this->articulo->mecanico_id,
                'commissionable_type' => 'App\Models\Trabajador',
                'detalle_venta_id' => $this->id,
            ])->first();

            if (!$comisionExistente) {
                // Calcular el monto total de la comisión (costo × cantidad)
                $montoComision = $this->articulo->costo_mecanico * $this->cantidad;

                // Crear la comisión
                return Comision::create([
                    'commissionable_id' => $this->articulo->mecanico_id,
                    'commissionable_type' => 'App\Models\Trabajador',
                    'tipo_comision' => 'mecanico',
                    'monto' => $montoComision,
                    'detalle_venta_id' => $this->id,
                    'venta_id' => $this->venta_id,
                    'articulo_id' => $this->articulo_id,
                    'fecha_calculo' => now(),
                    'estado' => 'pendiente',
                ]);
            }

            return $comisionExistente;
        }

        return null;
    }

    /**
     * Genera comisiones para trabajadores de car wash
     */
    public function generarComisionesCarwash()
    {
        $comisiones = collect([]);

        // Generar comisiones para cada trabajador asignado
        foreach ($this->trabajadoresCarwash as $trabajador) {
            $asignacion = TrabajadorDetalleVenta::where([
                'trabajador_id' => $trabajador->id,
                'detalle_venta_id' => $this->id
            ])->first();

            if ($asignacion) {
                $comision = $asignacion->generarComision();
                if ($comision) {
                    $comisiones->push($comision);
                }
            }
        }

        return $comisiones;
    }
}
