<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProveedorFormRequest extends FormRequest
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
            'nombre' => 'required|string|max:255|',
            'nit' => 'nullable|string|max:17',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'banco' => 'nullable|string|max:255',
            'nombre_cuenta' => 'nullable|string|max:255',
            'tipo_cuenta' => 'nullable|string|max:255',
            'numero_cuenta' => 'nullable|string|max:255'
        ];
    }
}
