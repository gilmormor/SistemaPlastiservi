<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarDTENC extends FormRequest
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
            'obs' => 'max:200',
            'centroeconomico_id' => 'required|max:20',
            'vendedor_id' => 'required',
            'usuario_id' => 'required',
            'codref' => [ //Codigo Referencia que se guarda en tabla dtencnd (Ya que este datos solo es para las nota de crédito y débito.)
                    'required',
                    function ($attribute, $value, $fail) {
                        if ($value < 1 or $value > 3) {
                            $fail($attribute . ': Códido de Referencia debe estar entre 1 y 3');
                        }
                    },
                ]
        ];

    }
}
