<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class OpStoreRequest extends FormRequest
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
            'otdet_id' => 'required|exists:otdet,id',
            'kgprod' => 'required|numeric|min:0',
            'prioridad' => 'required|numeric|max:3',
            'obs' => 'nullable|string',
            'EtapasProduccion' => 'required|array|min:1',
            'EtapasProduccion.*.apetapaprod_id' => 'required|exists:areaproduccionetapaprod,id',
            'EtapasProduccion.*.observacion' => 'nullable|string',
            'EtapasProduccion.*.maquina_id' => [
                                            'nullable',
                                            'integer',
                                            'min:0',
                                            function ($attribute, $value, $fail) {
                                                if ($value > 0 && !DB::table('maquina')->where('id', $value)->exists()) {
                                                    $fail("La máquina con ID {$value} no existe.");
                                                }
                                            },
            ],
        ];
    }
}
