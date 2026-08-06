<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarUsuAprobModulo extends FormRequest
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
            'usuario_id' => 'required|integer',
            'modulo'     => 'required|max:100',
            'usuario_creador_id'   => 'required|array|min:1',
            'usuario_creador_id.*' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            'usuario_creador_id.required' => 'Debe seleccionar al menos un usuario que pueda aprobar.',
        ];
    }
}
