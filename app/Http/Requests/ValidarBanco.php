<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarBanco extends FormRequest
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
            'nombre' => 'required|max:100|unique:banco,nombre,' . $this->route('id'),
            'desc' => 'required|max:100',
            'numcta' => 'required|max:30',
            'bancotipocta_id' => 'required|max:200'
        ];
    }
}
