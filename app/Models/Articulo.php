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
        'mecanico_id',
        'costo_mecanico',
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
        'stock' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'costo_mecanico' => 'decimal:2',
    ];

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
}
