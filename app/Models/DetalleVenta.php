<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
    }    public function usuario()
    {
        return $this->belongsTo(User::class);
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
    }    /**
     * Genera comisiones para trabajadores de car wash
     */
    public function generarComisionesCarwash($forzarRegeneracion = false)
    {
        $comisiones = collect([]);

        // Si se fuerza la regeneración, eliminar comisiones existentes
        if ($forzarRegeneracion) {
            Comision::where('detalle_venta_id', $this->id)
                   ->where('tipo_comision', 'carwash')
                   ->delete();
        }

        // Generar comisiones para cada trabajador asignado
        foreach ($this->trabajadoresCarwash as $trabajador) {
            $asignacion = TrabajadorDetalleVenta::where([
                'trabajador_id' => $trabajador->id,
                'detalle_venta_id' => $this->id
            ])->first();

            if ($asignacion && $asignacion->monto_comision > 0) {
                // Si estamos forzando regeneración, crear directamente sin verificar existencia
                if ($forzarRegeneracion) {
                    $detalleVenta = $this;
                    
                    $comision = Comision::create([
                        'commissionable_id' => $asignacion->trabajador_id,
                        'commissionable_type' => 'App\Models\Trabajador',
                        'tipo_comision' => 'carwash',
                        'monto' => $asignacion->monto_comision,
                        'detalle_venta_id' => $this->id,
                        'venta_id' => $detalleVenta->venta_id,
                        'articulo_id' => $detalleVenta->articulo_id,
                        'fecha_calculo' => now(),
                        'estado' => 'pendiente',
                    ]);
                    
                    if ($comision) {
                        $comisiones->push($comision);
                    }
                } else {
                    // Usar el método normal que verifica existencia
                    $comision = $asignacion->generarComision();
                    if ($comision) {
                        $comisiones->push($comision);
                    }
                }
            }
        }

        return $comisiones;
    }

    /**
     * Asigna trabajadores a este detalle de venta con el monto de comisión especificado
     *
     * @param array|collection $trabajadores Array de IDs de trabajadores o colección de objetos Trabajador
     * @param float|null $montoComision Monto de comisión individual (si es null, se usa la comisión del artículo)
     * @return array Asignaciones creadas
     */
    public function asignarTrabajadores($trabajadores, $montoComision = null)
    {
        $asignaciones = [];

        // Si no hay trabajadores, salir temprano
        if (empty($trabajadores)) {
            Log::warning("No se recibieron trabajadores para asignar al detalle de venta ID: {$this->id}");
            return $asignaciones;
        }

        // Registrar información sobre los trabajadores recibidos
        Log::info("Asignando trabajadores al detalle de venta ID: {$this->id}", [
            'trabajadores_recibidos' => $trabajadores,
            'tipo_dato' => gettype($trabajadores),
            'clase' => is_object($trabajadores) ? get_class($trabajadores) : 'no es objeto'
        ]);

        // Si no se proporciona un monto de comisión, usar el configurado en el artículo
        if ($montoComision === null && $this->articulo) {
            $montoComision = $this->articulo->comision_carwash;
            Log::info("Usando monto de comisión del artículo: {$montoComision}", []);
        }

        // Asegurarse de que el monto sea válido
        if ($montoComision === null || $montoComision <= 0) {
            $montoComision = 0;
            Log::warning("El monto de comisión es inválido o cero.");
        }

        // Convertir a array si es una colección o un valor único
        $trabajadorIds = [];

        if (is_array($trabajadores)) {
            // Ya es un array
            $trabajadorIds = array_map('intval', $trabajadores); // Convertir a enteros
        } else if (is_object($trabajadores) && method_exists($trabajadores, 'pluck')) {
            // Es una colección
            $trabajadorIds = $trabajadores->pluck('id')->toArray();
        } else if (is_string($trabajadores) || is_numeric($trabajadores)) {
            // Es un ID único
            $trabajadorIds = [(int)$trabajadores]; // Convertir a entero
        }

        // Eliminar valores vacíos, nulos o cero
        $trabajadorIds = array_filter($trabajadorIds, function($id) {
            return !empty($id) && intval($id) > 0;
        });

        // Eliminar duplicados
        $trabajadorIds = array_unique($trabajadorIds);

        // Si después de todo no hay IDs, salir
        if (empty($trabajadorIds)) {
            Log::warning("No se pudieron procesar IDs de trabajadores para el detalle {$this->id}");
            return $asignaciones;
        }

        Log::info("IDs de trabajadores a procesar: " . implode(', ', $trabajadorIds));

        // Validar que los trabajadores sean de tipo carwash
        $trabajadoresValidos = Trabajador::whereIn('id', $trabajadorIds)->get();

        Log::info("Trabajadores válidos encontrados: " . $trabajadoresValidos->count(), []);        // Eliminar asignaciones actuales para evitar duplicados
        $this->trabajadoresCarwash()->detach();
        
        // Refrescar la instancia para asegurar que las relaciones se actualicen
        $this->refresh();

        // Crear asignaciones nuevas
        foreach ($trabajadoresValidos as $trabajador) {
            try {
                // Crear la asignación
                $asignacion = TrabajadorDetalleVenta::create([
                    'trabajador_id' => $trabajador->id,
                    'detalle_venta_id' => $this->id,
                    'monto_comision' => $montoComision
                ]);

                $asignaciones[] = $asignacion;
                Log::info("Trabajador ID {$trabajador->id} asignado al detalle {$this->id} con comisión {$montoComision}", []);
            } catch (\Exception $e) {
                Log::error("Error al asignar trabajador ID {$trabajador->id}: " . $e->getMessage());
            }
        }

        // Verificar que las asignaciones se hayan creado correctamente
        $asignacionesCreadas = $this->trabajadoresCarwash()->count();
        Log::info("Total de asignaciones creadas: {$asignacionesCreadas} para el detalle {$this->id}", []);

        return $asignaciones;
    }

    /**
     * Genera las comisiones asociadas a este detalle de venta
     * (para mecánicos y trabajadores de carwash asignados)
     *
     * @return array Comisiones generadas
     */
    public function generarComisiones()
    {
        $comisiones = [];

        // Si hay un artículo asociado y es un servicio, generar comisión para el mecánico
        if ($this->articulo && $this->articulo->tipo == 'servicio') {
            // Comisión para el mecánico
            $comisionMecanico = $this->generarComisionMecanico();
            if ($comisionMecanico) {
                $comisiones[] = $comisionMecanico;
            }

            // Comisiones para trabajadores de carwash
            $comisionesCarwash = $this->generarComisionesCarwash();
            if ($comisionesCarwash && count($comisionesCarwash) > 0) {
                foreach ($comisionesCarwash as $comision) {
                    $comisiones[] = $comision;
                }
            }
        }

        return $comisiones;
    }

    /**
     * Calcula el total de comisiones asociadas a este detalle de venta
     *
     * @return float Monto total de comisiones
     */
    public function calcularComisiones()
    {
        $total = 0;

        // Comisión del mecánico (si aplica)
        if ($this->articulo &&
            $this->articulo->tipo === 'servicio' &&
            $this->articulo->mecanico_id &&
            $this->articulo->costo_mecanico > 0) {

            $total += $this->articulo->costo_mecanico * $this->cantidad;
        }

        // Comisiones de carwasheros
        $comisionesCarwash = $this->trabajadoresCarwash()->sum('monto_comision');
        $total += $comisionesCarwash;

        return $total;
    }
}
