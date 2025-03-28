<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'vehiculo_id',
        'numero_factura',
        'fecha',
        'tipo_venta',
        'usuario_id',
        'estado',
        'estado_pago'
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Relación con el usuario (vendedor) que realizó la venta.
     * Esta relación usa usuario_id que está en fillable.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    /**
     * Obtiene los pagos asociados a esta venta.
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    /**
     * Alias de usuario() para compatibilidad.
     * Esta relación usa el mismo campo que usuario().
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Alias adicional para compatibilidad si se usa 'vendedor'.
     * Esta relación usa el mismo campo que usuario().
     */
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
