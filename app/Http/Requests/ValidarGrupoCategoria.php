<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarGrupoCategoria extends FormRequest
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
            'gruc_nombre' => 'required|max:100|unique:grupocategoria,gruc_nombre,' . $this->route('id'),
            'gruc_descripcion' => 'required|max:100'
        ];
    }
}
