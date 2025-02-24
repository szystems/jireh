<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class IngresoEditFormRequest extends FormRequest
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
            'detalles.new.*.articulo_id'   => 'nullable|exists:articulos,id',
            'detalles.new.*.precio_compra' => 'nullable|numeric|min:0',
            'detalles.new.*.precio_venta'  => 'nullable|numeric|min:0',
            'detalles.new.*.cantidad'      => 'nullable|numeric|min:0.01',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->detalles) || count($this->detalles) < 1) {
                $validator->errors()->add('detalles', 'Debe haber al menos un artículo en los detalles.');
            }
        });
    }
}
