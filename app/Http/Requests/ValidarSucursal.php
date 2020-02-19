<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarSucursal extends FormRequest
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
            'nombre' => 'required|max:100|unique:sucursal,nombre,' . $this->route('id'),
            'direccion' => 'required|max:250',
            'telefono1' => 'required|max:14',
            'telefono2' => 'max:14',
            'telefono3' => 'max:14',
            'email' => 'required|max:50',
        ];
    }
}
