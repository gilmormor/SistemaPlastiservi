<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarPersona extends FormRequest
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
            'rut' => 'required|max:14|unique:persona,rut,' . $this->route('id'),
            'nombre' => 'required|max:60',
            'apellido' => 'required|max:60',
            'direccion' => 'required|max:100',
            'telefono' => 'required|max:100',
            'ext' => 'required|max:10',
            'email' => 'required|max:100|email|unique:persona,email,' . $this->route('id'),
            'cargo_id' => 'required|max:20',
            'activo' => 'required|max:1',
            //'usuario_id' => 'max:20|unique:persona,usuario_id,' . $this->route('id'),
        ];
    }
}
