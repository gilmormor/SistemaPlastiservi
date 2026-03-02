<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarInsumo extends FormRequest
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
            'nombre' => 'required|max:50|unique:insumo,nombre,' . $this->route('id'),
            'desc' => 'required|max:100',
            'tipocosto_id' => 'required',
            'unidadmedida_id' => 'required',
            'costounitario' => 'required',
            'activo' => 'required'
        ];
    }
}