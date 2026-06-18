<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarCcParam extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre'   => 'required|max:50',
            'etiqueta' => 'required|max:100',
            'tipo'     => 'required|in:number,text,boolean',
            'unidad'   => 'nullable|max:20',
            'decimales'=> 'required|integer|min:0|max:6',
            'orden'    => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required'   => 'El nombre interno es obligatorio.',
            'etiqueta.required' => 'La etiqueta visible es obligatoria.',
            'tipo.required'     => 'El tipo de dato es obligatorio.',
            'tipo.in'           => 'El tipo debe ser: number, text o boolean.',
        ];
    }
}
