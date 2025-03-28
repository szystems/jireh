<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrabajadorFormRequest extends FormRequest
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
            'email' => [
                'nullable',
                'email',
                Rule::unique('trabajadors')->ignore($this->route('id')),
            ],
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'no_documento' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('trabajadors')->ignore($this->route('id')),
            ],
            'fecha_nacimiento' => 'nullable|date',
            'estado' => 'required|string|in:activo,inactivo',
        ];
    }
}
