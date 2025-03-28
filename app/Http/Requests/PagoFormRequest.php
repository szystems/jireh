<?php

namespace App\Http\Requests;

use App\Models\Pago;
use App\Models\Venta;
use App\Models\Descuento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PagoFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Cambiado a true para permitir la validación
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $ventaId = $this->input('venta_id');
        $pagoId = $this->route('id'); // Obtener ID del pago si es una actualización

        return [
            'venta_id' => 'required|exists:ventas,id',
            'fecha' => 'required|date',
            'monto' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($ventaId, $pagoId) {
                    // Obtener la venta y sus detalles
                    $venta = Venta::with('detalleVentas', 'pagos')->find($ventaId);
                    if (!$venta) {
                        return $fail('La venta especificada no existe.');
                    }

                    // Calcular el total de la venta
                    $totalVenta = 0;
                    foreach ($venta->detalleVentas as $detalle) {
                        $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                        $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                        // Calcular descuento
                        $montoDescuento = 0;
                        if ($detalle->descuento_id) {
                            $descuento = Descuento::find($detalle->descuento_id);
                            if ($descuento) {
                                $montoDescuento = $subtotalSinDescuento * ($descuento->porcentaje_descuento / 100);
                            }
                        }

                        $totalVenta += ($subtotalSinDescuento - $montoDescuento);
                    }

                    // Calcular el total ya pagado (excluyendo el pago actual si estamos actualizando)
                    $totalPagado = 0;
                    foreach ($venta->pagos as $pago) {
                        if (!$pagoId || $pago->id != $pagoId) { // Excluir el pago actual si estamos actualizando
                            $totalPagado += $pago->monto;
                        }
                    }

                    // Saldo pendiente
                    $saldoPendiente = $totalVenta - $totalPagado;

                    // Verificar si el monto supera el saldo pendiente
                    if ($value > $saldoPendiente) {
                        $fail("El monto del pago ($value) supera el saldo pendiente ({$saldoPendiente}). No se puede procesar el pago.");
                    }
                }
            ],
            'metodo_pago' => [
                'required',
                Rule::in([
                    Pago::METODO_EFECTIVO,
                    Pago::METODO_TARJETA_CREDITO,
                    Pago::METODO_TARJETA_DEBITO,
                    Pago::METODO_TRANSFERENCIA,
                    Pago::METODO_CHEQUE,
                    Pago::METODO_OTRO
                ])
            ],
            'referencia' => 'nullable|string|max:255',
            'comprobante_imagen' => 'nullable|file|image|max:2048',
            'observaciones' => 'nullable|string',
            'usuario_id' => 'required|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'venta_id.required' => 'La venta es obligatoria.',
            'venta_id.exists' => 'La venta seleccionada no existe.',
            'fecha.required' => 'La fecha del pago es obligatoria.',
            'fecha.date' => 'El formato de fecha es incorrecto.',
            'monto.required' => 'El monto del pago es obligatorio.',
            'monto.numeric' => 'El monto debe ser un valor numérico.',
            'monto.min' => 'El monto debe ser mayor a cero.',
            'metodo_pago.required' => 'El método de pago es obligatorio.',
            'metodo_pago.in' => 'El método de pago seleccionado no es válido.',
            'usuario_id.required' => 'El usuario es obligatorio.',
            'usuario_id.exists' => 'El usuario seleccionado no existe.',
        ];
    }
}
