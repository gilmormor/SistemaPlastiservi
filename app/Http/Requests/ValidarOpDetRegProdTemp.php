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
            // kgprod = kg producidos (buenos). El operario ingresa este valor directamente.
            // kgent = kgprod + kgscrap se calcula en el controlador (no viene del form como campo validado).
            // Requiere al menos uno de kgprod o kgscrap > 0.
            'kgprod' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $kgprod  = $value ?: 0;
                    $kgscrap = $this->input('kgscrap') ?: 0;

                    if ($kgprod <= 0 && $kgscrap <= 0) {
                        $fail('Debe ingresar un valor mayor a 0 en Kg Producción o Kg Scrap.');
                    }
                },
            ],
            'kgscrap' => ['required', 'numeric', 'min:0'],
            'operario_id' => 'required|numeric',
            'usuario_id' => 'required|numeric',
        ];
    }
}
