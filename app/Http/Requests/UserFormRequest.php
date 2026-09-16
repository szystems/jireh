<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
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
        $rules = [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('id')),
            ],
            'name'=>'required|max:191',
            'fotografia' => 'mimes:jpg,jpeg,bmp,png,gif|max:3000|nullable',
            'fecha_nacimiento'=>'required|date',
            'telefono'=>'string|max:20|nullable',
            'celular'=>'string|max:20|nullable',
            'direccion'=>'string|max:500|nullable',
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if ($this->filled('password') && $this->isUpdatingOwnProfile()) {
            $rules['current_password'] = ['required', 'string'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'current_password.required' => 'Debe ingresar su contraseña actual para cambiarla.',
        ];
    }

    private function isUpdatingOwnProfile(): bool
    {
        $id = $this->route('id');

        return $id && auth()->check() && (int) $id === (int) auth()->id();
    }
}
