<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarCliente extends FormRequest
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
            'rut' => 'required|max:12|unique:cliente,rut,' . $this->route('id'),
            'razonsocial' => 'required|max:70|unique:cliente,razonsocial,' . $this->route('id'),
            'direccion' => 'required|max:200',
            'telefono' => 'required|max:50',
            'email' => 'required|max:50',
            'nombrefantasia' => 'max:50',
            'direccion' => 'required',
            'giro_id' => 'required',
            'regionp_id' => 'required',
            'provinciap_id' => 'required',
            'comunap_id' => 'required',
            'formapago_id' => 'required',
            'plazopago_id' => 'required',
            'contactonombre' => 'required|max:50',
            'contactoemail' => 'required|max:50',
            'contactotelef' => 'required|max:50',
            'finanzascontacto' => 'required|max:50',
            'finanzanemail' => 'required|max:50',
            'finanzastelefono' => 'required|max:50'
        ];
    }
}
