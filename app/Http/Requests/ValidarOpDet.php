<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarOpDet extends FormRequest
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
            'op_id' => 'required',
            'otdet_id' => 'required',
            'obs' => 'required',
            'maquina_id' => 'required',
            'usuariodel_id' => 'required'
        ];
    }
}
