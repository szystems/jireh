<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadors';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'direccion',
        'email',
        'nit',
        'dpi',
        'tipo_trabajador_id',
        'estado',
    ];

    /**
     * Relación con el tipo de trabajador
     */
    public function tipoTrabajador()
    {
        return $this->belongsTo(TipoTrabajador::class, 'tipo_trabajador_id');
    }

    /**
     * Obtener el nombre completo del trabajador
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }
}
