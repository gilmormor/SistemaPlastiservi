<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarClienteDesBloqueado extends FormRequest
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
            'cliente_id' => 'required|max:6',
            //'cliente_id' => 'required|max:6|unique:clientedesbloqueado,cliente_id,' . $this->route('id'). ',id,deleted_at,NULL',
            'obs' => 'required|max:100'
        ];
    }
}
