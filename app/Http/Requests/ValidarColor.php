<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarColor extends FormRequest
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
            'nombre' => 'required|max:60|unique:color,nombre,' . $this->route('id'),
            'descripcion' => 'required|max:100',
            'codcolor' => 'required|max:20|unique:color,codcolor,' . $this->route('id'),
        ];
    }
}
