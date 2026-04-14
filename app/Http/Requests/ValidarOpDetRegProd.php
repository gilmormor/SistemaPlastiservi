<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarOpDetRegProd extends FormRequest
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
            //'codintprod' => ['numeric','max:12', new ValidarCampoCodIntProd],
            //'codbarra' => ['numeric','max:45', new ValidarCampoCodBarraProd],
            'clase' => 'required|max:6',
            //'diamext' => 'required|numeric',
            'diametro' => 'required',
            'espesor' => 'required|numeric',
            'long' => 'required|numeric',
            'peso' => 'required|numeric',
            'precioneto' => 'required|numeric',
            'categoriaprod_id' => 'required|numeric',
            'claseprod_id' => 'required|numeric',
            'estado' => 'required'
        ];
    }
}
