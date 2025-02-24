<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleIngreso extends Model
{
    use HasFactory;

    protected $fillable = ['ingreso_id', 'articulo_id', 'precio_compra', 'precio_venta', 'cantidad'];

    public function ingreso()
    {
        return $this->belongsTo(Ingreso::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
}
