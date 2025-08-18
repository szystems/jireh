<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PagoSueldo extends Model
{
    use HasFactory;

    protected $table = 'pagos_sueldos';

    protected $fillable = [
        'numero_lote',
        'periodo_mes',
        'periodo_anio',
        'fecha_pago',
        'metodo_pago',
        'estado',
        'total_monto',
        'observaciones',
        'comprobante_pago',
        'usuario_creo_id'
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'total_monto' => 'decimal:2',
        'periodo_mes' => 'integer',
        'periodo_anio' => 'integer'
    ];

    // Relaciones
    public function detalles()
    {
        return $this->hasMany(DetallePagoSueldo::class, 'pago_sueldo_id');
    }

    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'usuario_creo_id');
    }

    // Alias para mantener compatibilidad con las vistas
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_creo_id');
    }

    // Scopes
    public function scopePorAnio($query, $año)
    {
        $query->where('periodo_anio', $año);
        return $query;
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    // Mutadores
    public function setNumeroLoteAttribute($value)
    {
        $this->attributes['numero_lote'] = strtoupper($value);
    }

    // Accessors
    public function getPeriodoCompletoAttribute()
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $meses[$this->periodo_mes] . ' ' . $this->periodo_anio;
    }

    public function getPeriodoCortoAttribute()
    {
        $mesesCortos = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];
        
        return $mesesCortos[$this->periodo_mes] . ' ' . $this->periodo_anio;
    }

    public static function getNombreMes($numeroMes)
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $meses[$numeroMes] ?? 'Mes inválido';
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'pendiente' => 'bg-warning',
            'completado' => 'bg-success',
            'anulado' => 'bg-danger'
        ];
        
        return $badges[$this->estado] ?? 'bg-secondary';
    }

    // Métodos de negocio
    public static function generarNumeroLote($año, $mes)
    {
        $prefijo = "PS-{$año}" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-";
        
        $ultimoNumero = self::where('numero_lote', 'LIKE', $prefijo . '%')
            ->orderBy('numero_lote', 'desc')
            ->first();
        
        if ($ultimoNumero) {
            $numero = (int) substr($ultimoNumero->numero_lote, -3) + 1;
        } else {
            $numero = 1;
        }
        
        return $prefijo . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }

    public static function generarProximoNumero()
    {
        $fechaActual = Carbon::now();
        $año = $fechaActual->year;
        $mes = $fechaActual->month;
        
        return self::generarNumeroLote($año, $mes);
    }

    public function calcularTotalMonto()
    {
        $this->total_monto = $this->detalles()->sum('total_pagar');
        $this->save();
        return $this->total_monto;
    }

    public function puedeSerEditado()
    {
        return $this->estado === 'pendiente';
    }

    public function puedeSerAnulado()
    {
        return in_array($this->estado, ['pendiente', 'completado']);
    }

    // Métodos para el sistema de estados granular
    public function verificarEstadoCompleto()
    {
        // Obtener conteos de estados de los detalles
        $totalDetalles = $this->detalles()->count();
        $detallesPagados = $this->detalles()->where('estado', 'pagado')->count();
        $detallesCancelados = $this->detalles()->where('estado', 'cancelado')->count();
        $detallesPendientes = $this->detalles()->where('estado', 'pendiente')->count();

        // Solo cambiar estado si no está ya marcado como 'pagado'
        if ($this->estado !== 'pagado') {
            // Si todos los detalles están pagados, marcar lote como pagado
            if ($totalDetalles > 0 && $detallesPagados === $totalDetalles) {
                $this->update(['estado' => 'pagado']);
            }
            // Si hay algún detalle pagado pero no todos, mantener como pendiente
            // Si todos están cancelados, podríamos considerar cancelar el lote también
        }

        return [
            'total' => $totalDetalles,
            'pagados' => $detallesPagados,
            'cancelados' => $detallesCancelados,
            'pendientes' => $detallesPendientes,
            'progreso' => $totalDetalles > 0 ? ($detallesPagados / $totalDetalles) * 100 : 0
        ];
    }

    public function getProgresoPageAttribute()
    {
        return $this->verificarEstadoCompleto();
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

    // Método para obtener resumen de pagos
    public function getResumenPagos()
    {
        return [
            'total_empleados' => $this->detalles()->count(),
            'empleados_pagados' => $this->detalles()->where('estado', 'pagado')->count(),
            'empleados_pendientes' => $this->detalles()->where('estado', 'pendiente')->count(),
            'empleados_cancelados' => $this->detalles()->where('estado', 'cancelado')->count(),
            'monto_pagado' => $this->detalles()->where('estado', 'pagado')->sum('total_pagar'),
            'monto_pendiente' => $this->detalles()->where('estado', 'pendiente')->sum('total_pagar'),
            'progreso_porcentaje' => $this->verificarEstadoCompleto()['progreso']
        ];
    }
}
