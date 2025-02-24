<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngresoFormRequest extends FormRequest
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
            'numero_factura'           => 'nullable|string|max:255',
            'fecha'                    => 'required|date',
            'proveedor_id'             => 'required|exists:proveedors,id',
            'usuario_id'               => 'nullable|exists:users,id',
            'tipo_compra'              => 'required|in:Car Wash,CDS',
            'detalles'                 => 'required|array',
            'detalles.*.articulo_id'   => 'required|exists:articulos,id',
            'detalles.*.precio_compra' => 'required|numeric|min:0',
            'detalles.*.precio_venta'  => 'required|numeric|min:0',
            'detalles.*.cantidad'      => 'required|numeric|min:0.01',
        ];
    }
}
