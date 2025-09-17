<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    protected $fillable = [
        'cliente_id',
        'vehiculo_id',
        'numero_cotizacion',
        'fecha_cotizacion',
        'fecha_vencimiento',
        'tipo_cotizacion',
        'usuario_id',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_cotizacion' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    /**
     * Boot del modelo para generar número automático y fecha de vencimiento
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cotizacion) {
            // Generar número de cotización automático
            if (empty($cotizacion->numero_cotizacion)) {
                $lastCotizacion = static::orderBy('id', 'desc')->first();
                $nextNumber = $lastCotizacion ? (intval(substr($lastCotizacion->numero_cotizacion, 4)) + 1) : 1;
                $cotizacion->numero_cotizacion = 'COT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }

            // Establecer fecha de vencimiento (15 días por defecto)
            if (empty($cotizacion->fecha_vencimiento) && !empty($cotizacion->fecha_cotizacion)) {
                $cotizacion->fecha_vencimiento = Carbon::parse($cotizacion->fecha_cotizacion)->addDays(15);
            }
        });
    }

    /**
     * Relación con cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con vehículo
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Relación con usuario (vendedor) que realizó la cotización
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Alias para compatibilidad
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Alias para vendedor
     */
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación con detalles de cotización
     */
    public function detalleCotizaciones()
    {
        return $this->hasMany(DetalleCotizacion::class);
    }

    /**
     * Accessor para calcular el total de la cotización
     */
    public function getTotalAttribute()
    {
        return $this->detalleCotizaciones->sum('sub_total');
    }

    /**
     * Verifica si la cotización está vencida
     */
    public function getEstaVencidaAttribute()
    {
        return Carbon::now()->gt($this->fecha_vencimiento);
    }

    /**
     * Verifica si la cotización está vigente
     */
    public function getEstaVigenteAttribute()
    {
        return $this->fecha_vencimiento >= Carbon::now()->toDateString();
    }

    /**
     * Obtiene los días restantes de vigencia
     */
    public function getDiasRestantesAttribute()
    {
        $diasRestantes = Carbon::now()->diffInDays($this->fecha_vencimiento, false);
        return $diasRestantes > 0 ? $diasRestantes : 0;
    }

    /**
     * Scope para cotizaciones vigentes
     */
    public function scopeVigentes($query)
    {
        return $query->where('estado', 'vigente')
                    ->where('fecha_vencimiento', '>=', Carbon::now());
    }

    /**
     * Scope para cotizaciones vencidas
     */
    public function scopeVencidas($query)
    {
        return $query->where('fecha_vencimiento', '<', Carbon::now())
                    ->where('estado', '!=', 'convertida');
    }

    /**
     * Accessor para obtener el estado real (combinando estado y vigencia)
     */
    public function getEstadoRealAttribute()
    {
        // Si está Aprobado, mantener ese estado
        if ($this->estado === 'Aprobado') {
            return 'Aprobado';
        }
        
        // Si está Generado, verificar si está vigente o vencida por fecha
        if ($this->estado === 'Generado') {
            return $this->esta_vigente ? 'vigente' : 'vencida';
        }
        
        // Para cualquier otro estado, devolver tal como está
        return $this->estado;
    }

    /**
     * Accessor para obtener el badge CSS del estado
     */
    public function getEstadoBadgeAttribute()
    {
        switch ($this->estado_real) {
            case 'vigente':
                return '<span class="badge bg-success">Vigente</span>';
            case 'vencida':
                return '<span class="badge bg-danger">Vencida</span>';
            case 'aprobada':
                return '<span class="badge bg-primary">Aprobada</span>';
            case 'generada':
                return '<span class="badge bg-info">Generada</span>';
            default:
                return '<span class="badge bg-secondary">' . ucfirst($this->estado) . '</span>';
        }
    }

    /**
     * Actualiza automáticamente el estado basado en la fecha de vencimiento
     */
    public function actualizarEstado()
    {
        if ($this->esta_vencida && $this->estado === 'vigente') {
            $this->update(['estado' => 'vencida']);
        }
    }
}
