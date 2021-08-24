<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarGuiaDespInt extends FormRequest
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
            'fechahora' => 'required',
            'clienteinterno_id' => 'required',
            'cli_rut' => 'required',
            'cli_nom' => 'required',
            'cli_dir' => 'required',
            'cli_tel' => 'required',
            'cli_email' => 'required|max:50',
            'observacion' => 'max:200',
            'plazoentrega' => 'required',
            'lugarentrega' => 'required|max:100',
            'comunaentrega_id' => 'required',
            'total' => 'required',
            'usuario_id' => 'required',
            'sucursal_id' => 'required',
            'formapago_id' => 'required',
            'vendedor_id' => 'required',
            'plazopago_id' => 'required',
            'tipoentrega_id' => 'required',
            'comuna_id' => 'required',
            'total' => 'required|numeric|min:1'
        ];
    }
}
