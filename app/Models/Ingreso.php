<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;

    protected $fillable = ['numero_factura', 'fecha', 'proveedor_id', 'tipo_compra', 'usuario_id'];

    public function detalles()
    {
        return $this->hasMany(DetalleIngreso::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
