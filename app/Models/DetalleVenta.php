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
}
