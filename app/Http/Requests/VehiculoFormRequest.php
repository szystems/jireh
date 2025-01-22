<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehiculoFormRequest extends FormRequest
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
            'cliente_id' => 'required|exists:clientes,id', // Asegúrate de que el cliente existe
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'ano' => 'required|integer|between:1900,' . date('Y'), // Asegúrate de que el año sea válido
            'color' => 'required|string|max:100',
            'placa' => [
                'nullable', // Permite que el campo sea vacío
                'string',
                Rule::unique('vehiculos')->ignore($this->route('id')),
            ],
            'vin' => [
                'nullable', // Permite que el campo sea vacío
                'string',
                Rule::unique('vehiculos')->ignore($this->route('id')),
            ],
            'fotografia' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /**
     * Obtener los mensajes de error personalizados para las reglas de validación.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'cliente_id.required' => 'El cliente es obligatorio.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',
            'marca.required' => 'La marca es obligatoria.',
            'modelo.required' => 'El modelo es obligatorio.',
            'anio.required' => 'El año es obligatorio.',
            'anio.between' => 'El año debe estar entre 1900 y el año actual.',
            'color.required' => 'El color es obligatorio.',
            'placa.required' => 'La placa es obligatoria.',
            'placa.unique' => 'La placa ya está en uso.',
            'vin.required' => 'El VIN es obligatorio.',
            'vin.unique' => 'El VIN ya está en uso.',
        ];
    }
}
