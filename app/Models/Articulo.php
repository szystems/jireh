<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $table = 'articulos';
    protected $fillable = [
        'codigo',
        'nombre',
        'imagen',
        'descripcion',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'categoria_id',
        'unidad_id',
        'mecanico_id',        // Añadido para comisiones
        'costo_mecanico',     // Añadido para comisiones
        'comision_carwash',   // Añadido para comisiones
        'tipo',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock' => 'decimal:2', // Asegúrate que el stock se trate como decimal si puede tener fracciones
        'stock_minimo' => 'decimal:2',
        'costo_mecanico' => 'decimal:2',
    ];

    /**
     * Accesor para stock_disponible_venta.
     *
     * @return float
     */
    public function getStockDisponibleVentaAttribute()
    {
        // Por ahora, simplemente devuelve el stock.
        // Se puede añadir lógica más compleja si es necesario (ej. stock reservado).
        return (float) $this->attributes['stock'];
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    /**
     * Relación con el mecánico responsable del servicio
     */
    public function mecanico()
    {
        return $this->belongsTo(Trabajador::class, 'mecanico_id');
    }

    public function articulos()
    {
        return $this->belongsToMany(Articulo::class, 'servicio_articulo', 'servicio_id', 'articulo_id')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    /**
     * Relación con comisiones
     */
    public function comisiones()
    {
        return $this->hasMany(Comision::class);
    }

    /**
     * Determina si el artículo está agotado
     */
    public function estaAgotado()
    {
        return $this->stock <= 0;
    }

    /**
     * Determina si el artículo tiene stock bajo
     */
    public function tieneStockBajo()
    {
        return $this->stock > 0 && $this->stock <= $this->stock_minimo;
    }

    /**
     * Calcula el margen de ganancia del artículo
     */
    public function margenGanancia()
    {
        if ($this->precio_compra > 0) {
            return (($this->precio_venta - $this->precio_compra) / $this->precio_compra) * 100;
        }
        return 0;
    }

    /**
     * Verifica si el artículo es un servicio que requiere mecánico
     */
    public function requiereMecanico()
    {
        return $this->tipo == 'servicio';
    }

    /**
     * Genera comisión para el mecánico cuando se vende este servicio
     *
     * @param DetalleVenta $detalleVenta
     * @return Comision|null
     */
    public function generarComisionMecanico($detalleVenta)
    {
        // Solo generar comisión si es un servicio con mecánico asignado y tiene costo mayor a cero
        if ($this->tipo == 'servicio' && $this->mecanico_id && $this->costo_mecanico > 0) {
            // Verificar si ya existe una comisión para este detalle de venta
            $comisionExistente = Comision::where([
                'commissionable_id' => $this->mecanico_id,
                'commissionable_type' => 'App\Models\Trabajador',
                'detalle_venta_id' => $detalleVenta->id,
                'articulo_id' => $this->id,
            ])->first();

            if (!$comisionExistente) {
                // Crear la comisión
                return Comision::create([
                    'commissionable_id' => $this->mecanico_id,
                    'commissionable_type' => 'App\Models\Trabajador',
                    'tipo_comision' => 'mecanico',
                    'monto' => $this->costo_mecanico,
                    'detalle_venta_id' => $detalleVenta->id,
                    'venta_id' => $detalleVenta->venta_id,
                    'articulo_id' => $this->id,
                    'fecha_calculo' => now(),
                    'estado' => 'pendiente',
                ]);
            }

            return $comisionExistente;
        }

        return null;
    }
}
