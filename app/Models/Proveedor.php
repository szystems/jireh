<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedors';

    protected $primaryKey='id';

    protected $fillable = [
        'nombre',
        'nit',
        'contacto',
        'telefono',
        'celular',
        'direccion',
        'email',
        'banco',
        'nombre_cuenta',
        'tipo_cuenta',
        'numero_cuenta'
    ];

    public function articulos()
    {
        return $this->hasMany(Articulo::class);
    }
}
