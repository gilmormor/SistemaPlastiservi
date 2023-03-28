<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarDespachoSol extends FormRequest
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
            'notaventa_id' => 'required',
            'sucursal_id' => 'required',
            'usuario_id' => 'required',
            'comunaentrega_id' => 'required',
            'tipoentrega_id' => 'required',
            'plazoentrega' => 'required',
            'lugarentrega' => 'required',
            'contacto' => 'required|max:50',
            'contactoemail' => 'required|max:50|email:rfc', //'contactoemail' => 'required|max:50|email:rfc,dns',
            'contactotelf' => 'required|max:50',
            'observacion' => 'max:200',
            'fechaestdesp' => 'required',
        ];
    }
}
