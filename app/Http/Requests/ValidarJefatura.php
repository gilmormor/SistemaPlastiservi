<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarJefatura extends FormRequest
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
            'nombre' => 'required|max:255|unique:jefatura,nombre,' . $this->route('id'),
            'descripcion' => 'required|max:255',
            'abrev' => 'required|max:5|unique:jefatura,abrev,' . $this->route('id')
        ];
    }
}
