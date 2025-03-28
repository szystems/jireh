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
        'tipo_comision_vendedor_id',
        'tipo_comision_trabajador_id',
        'tipo',
        'estado',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function tipoComisionVendedor()
    {
        return $this->belongsTo(TipoComision::class, 'tipo_comision_vendedor_id');
    }

    public function tipoComisionTrabajador()
    {
        return $this->belongsTo(TipoComision::class, 'tipo_comision_trabajador_id');
    }

    public function articulos()
    {
        return $this->belongsToMany(Articulo::class, 'servicio_articulo', 'servicio_id', 'articulo_id')->withPivot('cantidad');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

}
