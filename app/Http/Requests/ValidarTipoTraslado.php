<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarTipoTraslado extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'indtraslado' => 'required|integer|unique:tipotraslado,indtraslado,' . $this->route('id'),
            'desc'        => 'required|max:100',
            'activo'      => 'required',
        ];
    }
}
