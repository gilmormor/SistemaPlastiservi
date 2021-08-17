<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarClienteInterno extends FormRequest
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
            'rut' => 'required|max:12|unique:clienteinterno,rut,' . $this->route('id'),
            'razonsocial' => 'required|max:70|unique:clienteinterno,razonsocial,' . $this->route('id'),
            'direccion' => 'required|max:200',
            'telefono' => 'required|max:50',
            'email' => 'required|max:50|email:rfc,dns',
            'comunap_id' => 'required',
            'formapago_id' => 'required',
            'plazopago_id' => 'required',
        ];
    }
}
