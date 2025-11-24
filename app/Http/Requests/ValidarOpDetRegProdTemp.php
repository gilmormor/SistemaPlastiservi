<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarOpDetRegProdTemp extends FormRequest
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
            'opdet_id' => 'required',
            'etapaprod_id' => 'required',
            'producto_id' => 'required',
            'sucursal_id' => 'required',
            'kg' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $kg = $value ?: 0;
                    $kgscrap = $this->input('kgscrap') ?: 0;

                    if ($kg <= 0 && $kgscrap <= 0) {
                        $fail('Debe ingresar un valor mayor a 0 en Kg o Kg Scrap.');
                    }
                },
            ],
            'kgscrap' => ['required', 'numeric', 'min:0'],
            'operario_id' => 'required|numeric',
            'usuario_id' => 'required|numeric',
        ];
    }
}
