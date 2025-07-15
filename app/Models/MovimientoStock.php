<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model
{
    use HasFactory;

    protected $table = 'movimientos_stock';

    protected $fillable = [
        'articulo_id',
        'tipo_movimiento',
        'cantidad',
        'cantidad_anterior',
        'cantidad_nueva',
        'referencia_tipo',
        'referencia_id',
        'observaciones',
        'usuario_id'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'cantidad_anterior' => 'decimal:2',
        'cantidad_nueva' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relaciones
    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePorArticulo($query, $articuloId)
    {
        return $query->where('articulo_id', $articuloId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_movimiento', $tipo);
    }

    public function scopeRecientes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }
}
