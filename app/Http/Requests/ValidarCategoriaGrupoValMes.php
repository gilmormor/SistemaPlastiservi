<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarCategoriaGrupoValMes extends FormRequest
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
            'grupoprod_id' => 'required',
            'unidadmedida_id' => 'required',
            'annomes' => 'required',
            'costo' => 'required|numeric',
            'metacomerkg' => 'required|numeric'
        ];
    }
}
