<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoTrabajador extends Model
{
    use HasFactory;

    protected $table = 'tipo_trabajadors';

    protected $fillable = [
        'nombre',
        'descripcion',
        'aplica_comision',
        'requiere_asignacion',
        'estado'
    ];

    /**
     * Relación con los trabajadores que pertenecen a este tipo
     */
    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class, 'tipo_trabajador_id');
    }

    /**
     * Comprobar si este tipo de trabajador está activo
     *
     * @return bool
     */
    public function isActivo()
    {
        return $this->estado == 'activo';
    }

    /**
     * Scope para obtener solo los tipos de trabajador activos
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
