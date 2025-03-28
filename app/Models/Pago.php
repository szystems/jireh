<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'venta_id',
        'fecha',
        'monto',
        'metodo_pago',
        'referencia',
        'comprobante_imagen',
        'observaciones',
        'usuario_id',
    ];

    /**
     * Constantes para los métodos de pago
     */
    const METODO_EFECTIVO = 'efectivo';
    const METODO_TARJETA_CREDITO = 'tarjeta_credito';
    const METODO_TARJETA_DEBITO = 'tarjeta_debito';
    const METODO_TRANSFERENCIA = 'transferencia';
    const METODO_CHEQUE = 'cheque';
    const METODO_OTRO = 'otro';

    /**
     * Lista de métodos de pago disponibles
     *
     * @var array
     */
    public static $metodosPago = [
        self::METODO_EFECTIVO => 'Efectivo',
        self::METODO_TARJETA_CREDITO => 'Tarjeta de Crédito',
        self::METODO_TARJETA_DEBITO => 'Tarjeta de Débito',
        self::METODO_TRANSFERENCIA => 'Transferencia Bancaria',
        self::METODO_CHEQUE => 'Cheque',
        self::METODO_OTRO => 'Otro',
    ];

    /**
     * Obtener la venta a la que pertenece este pago.
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Obtener el usuario que registró el pago.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Obtener el nombre del método de pago
     *
     * @return string
     */
    public function getNombreMetodoPagoAttribute()
    {
        return self::$metodosPago[$this->metodo_pago] ?? $this->metodo_pago;
    }
}
