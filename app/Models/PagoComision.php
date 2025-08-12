<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoComision extends Model
{
    use HasFactory;

    protected $table = 'pagos_comisiones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comision_id',
        'lote_pago_id',
        'monto',
        'metodo_pago',
        'usuario_id',
        'fecha_pago',
        'referencia',
        'comprobante_imagen',
        'observaciones',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    /**
     * Relación con la comisión
     */
    public function comision()
    {
        return $this->belongsTo(Comision::class);
    }

    /**
     * Relación con el lote de pago (nuevo)
     */
    public function lotePago()
    {
        return $this->belongsTo(LotePago::class, 'lote_pago_id');
    }

    /**
     * Relación con el usuario que registró el pago
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Marcar el pago como completado
     */
    public function completar()
    {
        $this->estado = 'completado';
        $saved = $this->save();

        // Actualizar el estado de la comisión
        if ($saved && $this->comision) {
            $this->comision->actualizarEstado();
        }

        return $saved;
    }

    /**
     * Marcar el pago como anulado
     */
    public function anular()
    {
        $this->estado = 'anulado';
        $saved = $this->save();

        // Actualizar el estado de la comisión
        if ($saved && $this->comision) {
            $this->comision->actualizarEstado();
        }

        return $saved;
    }
}
