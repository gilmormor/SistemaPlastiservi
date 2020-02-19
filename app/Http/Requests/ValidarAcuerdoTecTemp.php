<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarAcuerdoTecTemp extends FormRequest
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
            'nombreprod' => 'required|max:100|unique:acuerdotectemp,nombreprod,' . $this->route('id'),
            'entmuestra' => 'required|max:1',
            'matfrabobs' => 'max:60',
            'usoprevisto' => 'required|max:100',
            'uv' => 'required|max:1',
            'uvobs' => 'max:100',
            'antideslizante' => 'required|max:1',
            'antideslizanteobs' => 'max:100',
            'antiestatico' => 'required|max:1',
            'antiestaticoobs' => 'max:100',
            'antiblock' => 'required|max:1',
            'antiblockobs' => 'max:100',
            'aditivootro' => 'required|max:1',
            'aditivootroobs' => 'max:100',
            'ancho' => 'required|max:10',
            'anchoum' => 'required|max:5',
            'anchodesv' => 'max:100',
            'largo' => 'required|max:10',
            'largoum' => 'required|max:5',
            'largodesv' => 'max:100',
            'fuelle' => 'required|max:10',
            'fuelleum' => 'required|max:5',
            'fuelledesv' => 'max:100',
            'espesor' => 'required|max:10',
            'espesorum' => 'required|max:5',
            'espesordesv' => 'max:100',
            'npantone' => 'required|max:50',
            'translucidez' => 'required|max:1',
            'impreso' => 'required|max:1',
            'impresofoto' => 'max:100',
            'impresocolor_id' => 'max:20',
            'impresoobs' => 'max:100',
            'sfondo' => 'required|max:1',
            'sfondoobs' => 'max:100',
            'slateral' => 'required|max:1',
            'slateralobs' => 'max:100',
            'sprepicado' => 'required|max:1',
            'sprepicadoobs' => 'max:100',
            'slamina' => 'required|max:1',
            'slaminaobs' => 'max:100',
            'sfunda' => 'required|max:1',
            'sfundaobs' => 'max:100',
            'feunidxpaq' => 'max:10',
            'feunidxpaqobs' => 'max:100',
            'feunidxcont' => 'max:10',
            'feunidxcontobs' => 'max:100',
            'fecolorcont' => 'max:45',
            'fecolorcontobs' => 'max:100',
            'feunitxpalet' => 'max:10',
            'feunitxpaletobs' => 'max:100',
            'etiqplastiservi' => 'max:1',
            'etiqplastiserviobs' => 'max:100',
            'etiqotro' => 'max:100',
            'etiqotroobs' => 'max:100',
            'despacharA' => 'max:100',
            'fechacuerdocli' => 'max:10',
            'color_id' => 'required|max:20',
            'formapago_id' => 'required|max:20',
            'plazopago_id' => 'required|max:20',
            'vendedor_id' => 'required|max:20',
            'clientedirec_id' => 'required|max:20',
            'matfabr_id' => 'required|max:20',
            'usuariodel_id' => 'max:20',
        ];
    }
}
