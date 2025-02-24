<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipoComisionFormRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'porcentaje' => 'required|numeric|min:0|max:100',
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
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'porcentaje.required' => 'El porcentaje es obligatorio.',
            'porcentaje.numeric' => 'El porcentaje debe ser un número.',
            'porcentaje.min' => 'El porcentaje no puede ser menor que 0.',
            'porcentaje.max' => 'El porcentaje no puede ser mayor que 100.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
