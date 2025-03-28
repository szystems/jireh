<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'vehiculos';

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'cliente_id',
        'marca',
        'modelo',
        'ano',
        'color',
        'placa',
        'vin',
        'fotografia',
        'estado',
    ];

    // Relación con el modelo Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Añadir esta relación para conectar con las ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
