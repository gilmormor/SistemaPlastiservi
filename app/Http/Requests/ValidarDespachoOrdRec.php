<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarDespachoOrdRec extends FormRequest
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
            'despachoord_id' => 'required',
            'despachoordrecmotivo_id' => 'required',
            'obs' => 'max:200',
            'documento_id' => 'required_with:imagen',
            'imagen' => 'required_with:documento_id'
        ];
    }
    
    public function messages()
    {
        return [
            'documento_id.required_with' => 'El campo Nro Documento es requerido cuando Adjuntar documento está presente.',
            'imagen.required_with' => 'El campo Adjuntar documento es requerido cuando Nro documento está presente.'
        ];
    }
}
