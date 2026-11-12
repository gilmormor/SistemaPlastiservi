<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarUnidadMedida extends FormRequest
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
            /* Validaba contra la tabla color por un copy-paste: dejaba crear dos
               unidades con el mismo nombre y rechazaba una que se llamara igual que
               un color. Se agrega deleted_at,NULL para no contar las borradas,
               igual que en las demas validaciones del proyecto. */
            'nombre' => 'required|max:20|unique:unidadmedida,nombre,' . $this->route('id') . ',id,deleted_at,NULL',
            'descripcion' => 'required|max:100',
            'mostrarfact' => 'boolean',
            // marca si la unidad puede ser salida de una etapa de produccion
            'usa_produccion' => 'boolean'
        ];
    }
}
