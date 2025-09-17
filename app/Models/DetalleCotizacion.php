<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{
    use HasFactory;

    protected $table = 'detalle_cotizaciones';

    protected $fillable = [
        'cotizacion_id',
        'articulo_id',
        'cantidad',
        'precio_costo',
        'precio_venta',
        'descuento_id',
        'usuario_id',
        'sub_total',
        'porcentaje_impuestos',
    ];

    /**
     * Relación con cotización
     */
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Relación con artículo
     */
    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    /**
     * Relación con descuento
     */
    public function descuento()
    {
        return $this->belongsTo(Descuento::class);
    }

    /**
     * Relación con usuario que agregó el item
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calcula el subtotal automáticamente antes de guardar
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detalle) {
            $detalle->calcularSubTotal();
        });

        static::updating(function ($detalle) {
            $detalle->calcularSubTotal();
        });
    }

    /**
     * Calcula el subtotal considerando cantidad, precio y descuento
     */
    public function calcularSubTotal()
    {
        $subtotal = $this->cantidad * $this->precio_venta;
        
        // Aplicar descuento si existe
        if ($this->descuento) {
            $descuentoMonto = ($subtotal * $this->descuento->porcentaje) / 100;
            $subtotal -= $descuentoMonto;
        }
        
        // Aplicar impuestos si existen
        if ($this->porcentaje_impuestos > 0) {
            $impuestos = ($subtotal * $this->porcentaje_impuestos) / 100;
            $subtotal += $impuestos;
        }
        
        $this->sub_total = round($subtotal, 2);
    }

    /**
     * Accessor para obtener el monto del descuento aplicado
     */
    public function getMontoDescuentoAttribute()
    {
        if (!$this->descuento) {
            return 0;
        }
        
        $subtotalSinDescuento = $this->cantidad * $this->precio_venta;
        return round(($subtotalSinDescuento * $this->descuento->porcentaje) / 100, 2);
    }

    /**
     * Accessor para obtener el monto de impuestos aplicado
     */
    public function getMontoImpuestosAttribute()
    {
        if ($this->porcentaje_impuestos <= 0) {
            return 0;
        }
        
        $subtotalSinImpuestos = ($this->cantidad * $this->precio_venta) - $this->monto_descuento;
        return round(($subtotalSinImpuestos * $this->porcentaje_impuestos) / 100, 2);
    }

    /**
     * Accessor para obtener el total sin impuestos ni descuentos
     */
    public function getTotalBrutoAttribute()
    {
        return round($this->cantidad * $this->precio_venta, 2);
    }
}
