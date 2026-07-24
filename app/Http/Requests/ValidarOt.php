<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarOt extends FormRequest
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
            'fechahora' => 'required',
            'cliente_id' => 'required',
            'sucursal_id' => 'required',
            'obs' => 'max:100',
            'vendedor_id' => 'required',
            'kg' => 'required|numeric|min:1',
            'kgprod' => 'required|numeric|min:1',
            'neto' => 'required|numeric|min:1',
            'total' => 'required|numeric|min:1',
            /* 'oc_id' => 'required_with:oc_file',
            'oc_file' => 'required_with:oc_id', */
            'usuario_id' => 'required'
        ];
    }
}
