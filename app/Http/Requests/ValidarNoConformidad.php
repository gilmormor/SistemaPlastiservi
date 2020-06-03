<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarNoConformidad extends FormRequest
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
            'motivonc_id' => 'required',
            'puntonormativo' => 'max:250',
            'hallazgo' => 'required|max:250',
            'formadeteccionnc_id' => 'required',
            'puntonorma' => 'max:250'
        ];
    }
}
