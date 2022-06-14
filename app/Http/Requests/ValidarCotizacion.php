<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarCotizacion extends FormRequest
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
            'sucursal_id' => 'required',
            'fechahora' => 'required',
            'direccioncot' => 'required|max:200',
            'contacto' => 'required|max:50',
            'email' => 'required|max:50',
            'telefono' => 'required|max:50',
            'observacion' => 'max:200',
            'formapago_id' => 'required',
            'vendedor_id' => 'required',
            'plaentgadias' => 'min:1|max:45',
            'lugarentrega' => 'required|max:100',
            'plazopago_id' => 'required',
            'tipoentrega_id' => 'required',
            'region_id' => 'required',
            'provincia_id' => 'required',
            'comuna_id' => 'required',
            'neto' => 'required',
            'piva' => 'required',
            'iva' => 'required',
            'total' => 'required',
            'usuario_id' => 'required',
            'giro_id' => 'required',
            'neto' => 'required|numeric|min:1',
            'iva' => 'required|numeric|min:1',
            'total' => 'required|numeric|min:1'
            /*'cliente_id' => 'required_without:clientetemp_id',
            'clientetemp_id' => 'required_without:cliente_id'*/
        ];
    }
}
