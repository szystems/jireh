<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LotePago extends Model
{
    use HasFactory;

    protected $table = 'lotes_pago';

    protected $fillable = [
        'numero_lote',
        'fecha_pago',
        'metodo_pago',
        'referencia',
        'comprobante_imagen',
        'observaciones',
        'monto_total',
        'cantidad_comisiones',
        'estado',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto_total' => 'decimal:2',
        'cantidad_comisiones' => 'integer',
    ];

    // Relación con el usuario que creó el lote
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con los pagos de comisiones asociados
    public function pagosComisiones()
    {
        return $this->hasMany(PagoComision::class, 'lote_pago_id');
    }

    // Scope para filtrar por estado
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    // Scope para filtrar por rango de fechas
    public function scopeFechaPago($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_pago', [$fechaInicio, $fechaFin]);
        }
        return $query->whereDate('fecha_pago', $fechaInicio);
    }

    // Método para generar el número de lote automáticamente
    public static function generarNumeroLote()
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        
        // Buscar el último número de lote del día actual
        $prefijo = "LP-{$year}{$month}{$day}-";
        
        // Usar max() para obtener el número más alto del día
        $ultimoLote = self::where('numero_lote', 'LIKE', $prefijo . '%')
                          ->orderBy('numero_lote', 'desc')
                          ->first();
        
        $siguienteNumero = 1;
        
        if ($ultimoLote) {
            // Extraer el número del último lote
            $numeroActual = intval(substr($ultimoLote->numero_lote, -3));
            $siguienteNumero = $numeroActual + 1;
        }
        
        // Intentar hasta 100 veces para encontrar un número único
        for ($intento = 0; $intento < 100; $intento++) {
            $numeroLote = $prefijo . str_pad($siguienteNumero + $intento, 3, '0', STR_PAD_LEFT);
            
            // Verificar si ya existe
            $existe = self::where('numero_lote', $numeroLote)->exists();
            
            if (!$existe) {
                return $numeroLote;
            }
        }
        
        // Si no se pudo generar un número único, usar timestamp como fallback
        return $prefijo . str_pad(time() % 1000, 3, '0', STR_PAD_LEFT);
    }

    // Método para calcular el total y cantidad automáticamente
    public function recalcularTotales()
    {
        $pagos = $this->pagosComisiones();
        $this->monto_total = $pagos->sum('monto');
        $this->cantidad_comisiones = $pagos->count();
        $this->save();
    }
}
