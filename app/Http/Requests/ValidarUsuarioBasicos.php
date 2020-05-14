<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarUsuarioBasicos extends FormRequest
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
        if($this->route('id')){
            return [
                'nombre' => 'required|max:50',
                'email' => 'required|email|max:100|unique:usuario,email,' . $this->route('id'),
            ];
        }else{
            return [
                'nombre' => 'required|max:50',
                'email' => 'required|email|max:100' . $this->route('id'),
            ];
        }
    }
}
