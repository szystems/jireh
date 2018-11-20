<?php

namespace jireh\Http\Requests;

use jireh\Http\Requests\Request;

class VistavehiculoFormRequest extends Request
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
            'contacto' => 'required|max:50',
            'tel_contacto' => 'required|max:20',
            'email_contacto' => 'email|max:255',
            'nombre' => 'required|max:100',
            'marca' => 'required|max:45',
            'modelo' => 'required|max:45',
            'linea' => 'required|max:45',
            'tipo' => 'required|max:45',
            'origen' => 'required|max:45',
            'precio' => 'required',
            'puertas' => 'required|max:5',
            'motor' => 'required|max:45',
            'cilindros' => 'required|max:5',
            'combustible' => 'required|max:45',
            'millas' => 'required|max:45',
            'descripcion' => 'max:500',
            'ac' => 'required|max:5',
            'full_equipo' => 'required|max:5',
            'estado' => 'required|max:45'     
        ];
    }
}
