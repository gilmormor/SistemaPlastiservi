<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarDTE extends FormRequest
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
            'nrodocto' => 'max:20',
            'obs' => 'max:200',
            'tipodespacho' => 'required|max:1',
            'tipoentrega_id' => 'required|max:20',
            'comunaentrega_id' => 'required',
            'lugarentrega' => 'required',
            'centroeconomico_id' => 'required|max:20',
            'usuario_id' => 'required'
        ];
    }
}
