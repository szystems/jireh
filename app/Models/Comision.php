<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Comision extends Model
{
    use HasFactory;

    protected $table = 'comisiones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */    protected $fillable = [
        'commissionable_id',
        'commissionable_type',
        'tipo_comision',
        'monto',
        'porcentaje',
        'detalle_venta_id',
        'venta_id',
        'articulo_id',
        'fecha_calculo',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'monto' => 'decimal:2',
        'porcentaje' => 'decimal:2',
        'fecha_calculo' => 'date',
    ];

    /**
     * Obtener el modelo relacionado con esta comisión (trabajador o usuario)
     */
    public function commissionable()
    {
        return $this->morphTo();
    }

    /**
     * Relación con el detalle de venta asociado
     */
    public function detalleVenta()
    {
        return $this->belongsTo(DetalleVenta::class);
    }

    /**
     * Relación con la venta asociada
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Relación con el artículo asociado
     */
    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    /**
     * Relación con los pagos de comisión
     */
    public function pagos()
    {
        return $this->hasMany(PagoComision::class);
    }

    /**
     * Calcula el monto total pagado de la comisión
     */
    public function montoPagado()
    {
        return $this->pagos()->where('estado', 'completado')->sum('monto');
    }

    /**
     * Calcula el monto pendiente por pagar
     */
    public function montoPendiente()
    {
        return $this->monto - $this->montoPagado();
    }

    /**
     * Verifica si la comisión está completamente pagada
     */
    public function estaPagada()
    {
        return $this->estado === 'pagado' || $this->montoPendiente() <= 0;
    }

    /**
     * Actualiza automáticamente el estado basado en los pagos
     */
    public function actualizarEstado()
    {
        if ($this->montoPendiente() <= 0) {
            $this->estado = 'pagado';
        } else if ($this->pagos()->count() > 0) {
            $this->estado = 'parcial';
        } else {
            $this->estado = 'pendiente';
        }
        return $this->save();
    }

    /**
     * Scope para obtener solo comisiones pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para obtener comisiones del periodo actual (mes en curso)
     */
    public function scopeDelMesActual($query)
    {
        $inicio = Carbon::now()->startOfMonth();
        $fin = Carbon::now()->endOfMonth();
        return $query->whereBetween('fecha_calculo', [$inicio, $fin]);
    }

    /**
     * Scope para filtrar por tipo de receptor (usuario o trabajador)
     */
    public function scopePorTipoReceptor($query, $tipo)
    {
        if ($tipo === 'usuario') {
            return $query->where('commissionable_type', 'App\Models\User');
        } else if ($tipo === 'trabajador') {
            return $query->where('commissionable_type', 'App\Models\Trabajador');
        }
        return $query;
    }
}
