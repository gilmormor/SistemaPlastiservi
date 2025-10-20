<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarProduccionReg extends FormRequest
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
            'opdet_id' => 'required',
            'etapaprod_id' => 'required',
            'producto_id' => 'required',
            'sucursal_id' => 'required',
            'cant' => 'required|numeric',
            'kg' => 'required|numeric',
            'operario_id' => 'required|numeric',
            'usuario_id' => 'required|numeric',
        ];
    }
}
