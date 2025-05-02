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

    /**
     * Relación con detalles de venta (para mecánicos)
     */
    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'trabajador_id');
    }

    /**
     * Relación con servicios/artículos donde está asignado como mecánico
     */
    public function serviciosAsignados()
    {
        return $this->hasMany(Articulo::class, 'mecanico_id');
    }

    /**
     * Relación muchos a muchos con detalle ventas (para carwasheros)
     */
    public function detallesVentaCarwash()
    {
        return $this->belongsToMany(DetalleVenta::class, 'trabajador_detalle_venta')
                    ->withPivot('monto_comision')
                    ->withTimestamps();
    }

    /**
     * Relación polimórfica para comisiones
     */
    public function comisiones()
    {
        return $this->morphMany(Comision::class, 'commissionable');
    }

    /**
     * Obtiene las comisiones pendientes por pagar
     */
    public function comisionesPendientes()
    {
        return $this->comisiones()->where('estado', 'pendiente');
    }

    /**
     * Calcula el total de comisiones pendientes
     */
    public function totalComisionesPendientes()
    {
        return $this->comisionesPendientes()->sum('monto');
    }

    /**
     * Obtiene las comisiones pagadas
     */
    public function comisionesPagadas()
    {
        return $this->comisiones()->where('estado', 'pagado');
    }

    /**
     * Calcula el total de comisiones pagadas
     */
    public function totalComisionesPagadas()
    {
        return $this->comisionesPagadas()->sum('monto');
    }

    /**
     * Verifica si el trabajador es mecánico
     */
    public function esMecanico()
    {
        if (!$this->tipoTrabajador) return false;
        return $this->tipoTrabajador->nombre === 'Mecánico';
    }

    /**
     * Verifica si el trabajador es de car wash
     */
    public function esCarwash()
    {
        if (!$this->tipoTrabajador) return false;
        return $this->tipoTrabajador->nombre === 'Car Wash';
    }

    /**
     * Verifica si este trabajador puede recibir comisiones
     */
    public function puedeRecibirComisiones()
    {
        return $this->tipoTrabajador && $this->tipoTrabajador->aplica_comision;
    }

    /**
     * Verifica si este trabajador requiere ser asignado a servicios específicos
     * (como mecánicos)
     */
    public function requiereAsignacion()
    {
        return $this->tipoTrabajador && $this->tipoTrabajador->requiere_asignacion;
    }
}
