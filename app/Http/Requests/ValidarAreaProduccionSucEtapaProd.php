<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarAreaProduccionSucEtapaProd extends FormRequest
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
            'etapaprod_id' => 'required',
            'etapaprod_id' => 'required|array|min:1', // El campo debe ser un array y no estar vacío
            'etapaprod_id.*' => 'exists:etapaprod,id' // Cada valor debe existir en la tabla etapaprod
        ];
    }
}
