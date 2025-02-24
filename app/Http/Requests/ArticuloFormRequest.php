<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticuloFormRequest extends FormRequest
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
            'codigo' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $exists = \App\Models\Articulo::where('codigo', $value)
                            ->where('id', '!=', $this->route('articulo'))
                            ->exists();
                        if ($exists) {
                            $fail('El campo código ya ha sido tomado.');
                        }
                    }
                },
            ],
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'unidad_id' => 'required|exists:unidads,id',
            'tipo_comision_vendedor_id' => 'required|exists:tipo_comisions,id',
            'tipo_comision_trabajador_id' => 'required|exists:tipo_comisions,id',
            'tipo' => 'required|in:articulo,servicio',
            'estado' => 'boolean',
            'cantidades.*' => 'required|numeric|min:0',
            'articulos_servicio' => [
                function ($attribute, $value, $fail) {
                    if ($this->tipo == 'servicio' && empty($value)) {
                        $fail('Debe agregar al menos un artículo al servicio.');
                    }
                },
            ],
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'codigo.unique' => 'El código ya ha sido tomado.',
            'nombre.required' => 'El nombre es obligatorio.',
            'precio_compra.required' => 'El precio de compra es obligatorio.',
            'precio_compra.numeric' => 'El precio de compra debe ser un número.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.numeric' => 'El stock debe ser un número.',
            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'stock_minimo.numeric' => 'El stock mínimo debe ser un número.',
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'unidad_id.required' => 'La unidad de medida es obligatoria.',
            'unidad_id.exists' => 'La unidad de medida seleccionada no es válida.',
            'tipo_comision_vendedor_id.required' => 'El tipo de comisión para vendedor es obligatorio.',
            'tipo_comision_vendedor_id.exists' => 'El tipo de comisión para vendedor seleccionado no es válido.',
            'tipo_comision_trabajador_id.required' => 'El tipo de comisión para trabajador es obligatorio.',
            'tipo_comision_trabajador_id.exists' => 'El tipo de comisión para trabajador seleccionado no es válido.',
            'tipo.required' => 'El tipo es obligatorio.',
            'tipo.in' => 'El tipo debe ser artículo o servicio.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
            'cantidades.*.required' => 'La cantidad es obligatoria.',
            'cantidades.*.numeric' => 'La cantidad debe ser un número.',
        ];
    }
}
