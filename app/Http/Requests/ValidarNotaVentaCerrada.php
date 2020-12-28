<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarNotaVentaCerrada extends FormRequest
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
            'observacion' => 'max:200',
            'notaventa_id' => 'required',
            'motcierre_id' => 'required',
            'usuario_id' => 'required'
        ];
    }
}
