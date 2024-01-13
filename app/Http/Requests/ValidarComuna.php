<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarComuna extends FormRequest
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
            'nombre' => 'required|max:60|unique:comuna,nombre,' . $this->route('id'),
            'provincia_id' => 'required',
            'ciudad_id' => 'required',
        ];
    }
}
