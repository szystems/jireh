<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'numero_factura' => 'nullable|string|max:255',
            'fecha' => 'required|date',
            'tipo_venta' => 'required|in:Car Wash,CDS',
            'usuario_id' => 'required|exists:users,id',
            'estado' => 'nullable|boolean',
            'estado_pago' => 'required|in:pagado,pendiente',
            'detalles' => 'required|array',
            'detalles.*.articulo_id' => 'required|exists:articulos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.descuento_id' => 'nullable|exists:descuentos,id',
            'detalles.*.usuario_id' => 'required|exists:users,id',
            'detalles.*.sub_total' => 'required|numeric|min:0',
        ];
    }
}
