<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarUsuarioClave extends FormRequest
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
                'password' => 'nullable|min:5',
                're_password' => 'nullable|required_with:password|min:5|same:password',
            ];
        }else{
            return [
                'password' => 'required|min:5',
                're_password' => 'required|min:5|same:password',
            ];
        }
    }
}
