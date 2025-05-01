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
        'tipo_comision',
        'valor_comision',
        'porcentaje_comision',
        'permite_multiples_trabajadores',
        'configuracion_adicional',
        'estado'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'aplica_comision' => 'boolean',
        'requiere_asignacion' => 'boolean',
        'valor_comision' => 'decimal:2',
        'porcentaje_comision' => 'decimal:2',
        'permite_multiples_trabajadores' => 'boolean',
        'configuracion_adicional' => 'json'
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

    /**
     * Obtiene las opciones disponibles para el tipo de comisión
     */
    public static function getTiposComision()
    {
        return [
            'fijo' => 'Monto Fijo',
            'porcentaje_venta' => 'Porcentaje de la Venta',
            'porcentaje_ganancia' => 'Porcentaje de la Ganancia',
            'por_servicio' => 'Por Servicio Realizado',
            'personalizado' => 'Personalizado'
        ];
    }

    /**
     * Determina si este tipo de trabajador puede tener múltiples trabajadores asignados
     * a un mismo servicio (como en el caso de carwash)
     */
    public function permiteAsignacionMultiple()
    {
        return $this->permite_multiples_trabajadores;
    }

    /**
     * Determina si este tipo de trabajador es para mecánicos
     */
    public function esMecanico()
    {
        return $this->nombre === 'Mecánico' ||
               strtolower($this->nombre) === 'mecanico' ||
               $this->requiere_asignacion;
    }

    /**
     * Determina si este tipo de trabajador es para carwash
     */
    public function esCarwash()
    {
        return $this->nombre === 'Car Wash' ||
               strtolower($this->nombre) === 'carwash' ||
               $this->permite_multiples_trabajadores;
    }
}
