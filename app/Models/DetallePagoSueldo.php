<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePagoSueldo extends Model
{
    use HasFactory;

    protected $table = 'detalle_pagos_sueldos';

    protected $fillable = [
        'pago_sueldo_id',
        'trabajador_id',
        'usuario_id',
        'tipo_empleado',
        'sueldo_base',
        'horas_extra',
        'valor_hora_extra',
        'comisiones',
        'bonificaciones',
        'deducciones',
        'total_pagar',
        'observaciones',
        'estado',
        'fecha_pago',
        'observaciones_pago'
    ];

    protected $casts = [
        'sueldo_base' => 'decimal:2',
        'horas_extra' => 'decimal:2',
        'valor_hora_extra' => 'decimal:2',
        'comisiones' => 'decimal:2',
        'bonificaciones' => 'decimal:2',
        'deducciones' => 'decimal:2',
        'total_pagar' => 'decimal:2',
        'fecha_pago' => 'datetime'
    ];

    // Relaciones
    public function pagoSueldo()
    {
        return $this->belongsTo(PagoSueldo::class, 'pago_sueldo_id');
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Accessors
    public function getEmpleadoNombreAttribute()
    {
        if ($this->tipo_empleado === 'trabajador' && $this->trabajador) {
            return $this->trabajador->nombre . ' ' . $this->trabajador->apellido;
        } elseif ($this->tipo_empleado === 'vendedor' && $this->usuario) {
            return $this->usuario->name;
        }
        return 'Empleado no encontrado';
    }

    // Alias para compatibilidad con las vistas existentes
    public function getEmployeeNameAttribute()
    {
        return $this->getEmpleadoNombreAttribute();
    }

    public function getEmployeeTypeAttribute()
    {
        if ($this->tipo_empleado === 'trabajador') {
            return 'App\Models\Trabajador';
        } elseif ($this->tipo_empleado === 'vendedor') {
            return 'App\Models\User';
        }
        return null;
    }

    public function getEmployeeIdAttribute()
    {
        return $this->trabajador_id ?? $this->usuario_id;
    }

    // Relación dinámica para obtener el empleado
    public function getEmployeeAttribute()
    {
        if ($this->tipo_empleado === 'trabajador') {
            return $this->trabajador;
        } elseif ($this->tipo_empleado === 'vendedor') {
            return $this->usuario;
        }
        return null;
    }

    public function getEmpleadoTelefonoAttribute()
    {
        if ($this->tipo_empleado === 'trabajador' && $this->trabajador) {
            return $this->trabajador->celular ?? $this->trabajador->telefono;
        } elseif ($this->tipo_empleado === 'vendedor' && $this->usuario) {
            return $this->usuario->celular ?? $this->usuario->telefono;
        }
        return null;
    }

    public function getTipoEmpleadoBadgeAttribute()
    {
        $badges = [
            'trabajador' => 'bg-primary',
            'vendedor' => 'bg-success'
        ];
        
        return $badges[$this->tipo_empleado] ?? 'bg-secondary';
    }

    // Mutadores
    public function setTotalPagarAttribute($value)
    {
        // Auto-calcular si no se proporciona
        if (!$value) {
            $this->attributes['total_pagar'] = $this->sueldo_base + $this->bonificaciones - $this->deducciones;
        } else {
            $this->attributes['total_pagar'] = $value;
        }
    }

    // Métodos de negocio
    public function calcularTotal()
    {
        $this->total_pagar = $this->sueldo_base + $this->bonificaciones - $this->deducciones;
        return $this->total_pagar;
    }

    public static function boot()
    {
        parent::boot();

        // Validar que solo tenga trabajador_id O usuario_id
        static::saving(function ($model) {
            if (($model->trabajador_id && $model->usuario_id) || 
                (!$model->trabajador_id && !$model->usuario_id)) {
                throw new \Exception('Debe especificar exactamente un trabajador_id O un usuario_id, no ambos ni ninguno.');
            }

            // Auto-calcular total si no está definido
            if (!$model->total_pagar) {
                $model->total_pagar = $model->sueldo_base + $model->bonificaciones - $model->deducciones;
            }
        });

        // Actualizar total del lote principal al guardar
        static::saved(function ($model) {
            if ($model->pagoSueldo) {
                $model->pagoSueldo->calcularTotalMonto();
            }
        });

        // Actualizar total del lote principal al eliminar
        static::deleted(function ($model) {
            if ($model->pagoSueldo) {
                $model->pagoSueldo->calcularTotalMonto();
            }
        });
    }

    // Scopes
    public function scopePorTipoEmpleado($query, $tipo)
    {
        return $query->where('tipo_empleado', $tipo);
    }

    public function scopeTrabajadores($query)
    {
        return $query->where('tipo_empleado', 'trabajador');
    }

    public function scopeVendedores($query)
    {
        return $query->where('tipo_empleado', 'vendedor');
    }

    // Scopes para estados
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeCancelados($query)
    {
        return $query->where('estado', 'cancelado');
    }

    // Métodos de estado
    public function marcarComoPagado($observaciones = null)
    {
        $this->update([
            'estado' => 'pagado',
            'fecha_pago' => now(),
            'observaciones_pago' => $observaciones
        ]);

        // Verificar si todos los detalles del lote están pagados
        $this->pagoSueldo->verificarEstadoCompleto();
    }

    public function marcarComoCancelado($observaciones = null)
    {
        $this->update([
            'estado' => 'cancelado',
            'fecha_pago' => now(),
            'observaciones_pago' => $observaciones
        ]);
    }

    public function recalcularTotal()
    {
        $horasExtraTotal = $this->horas_extra * $this->valor_hora_extra;
        $totalBonificaciones = $this->bonificaciones + $horasExtraTotal + $this->comisiones;
        
        $this->update([
            'total_pagar' => $this->sueldo_base + $totalBonificaciones - $this->deducciones
        ]);
    }

    // Accessors para cálculos
    public function getTotalHorasExtraAttribute()
    {
        return $this->horas_extra * $this->valor_hora_extra;
    }

    public function getTotalBonificacionesCalculadoAttribute()
    {
        return $this->bonificaciones + $this->total_horas_extra + $this->comisiones;
    }

    public function getEstadoColorAttribute()
    {
        switch($this->estado) {
            case 'pendiente':
                return 'warning';
            case 'pagado':
                return 'success';
            case 'cancelado':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    public function getEstadoTextoAttribute()
    {
        switch($this->estado) {
            case 'pendiente':
                return 'Pendiente';
            case 'pagado':
                return 'Pagado';
            case 'cancelado':
                return 'Cancelado';
            default:
                return 'Desconocido';
        }
    }
}
