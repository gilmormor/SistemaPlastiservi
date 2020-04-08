<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarCategoriaProd extends FormRequest
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
            'nombre' => 'required|max:100|unique:categoriaprod,nombre,' . $this->route('id'),
            'descripcion' => 'required|max:200',
            'precio' => 'required|max:99999.99|numeric',
            'sta_precioxkilo' => 'required|max:200',
            'unidadmedida_id' => 'required|max:200',
            'unidadmedidafact_id' => 'required|max:200',
        ];
    }
}
