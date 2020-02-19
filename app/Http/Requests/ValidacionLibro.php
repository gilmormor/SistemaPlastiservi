<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidacionLibro extends FormRequest
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
            'titulo' => 'required|max:100|unique:libro,titulo,' . $this->route('id'),
            'isbn' => 'required|max:30|unique:libro,isbn,' . $this->route('id'),
            'autor' => 'required|max:100|unique:libro,autor,' . $this->route('id'),
            'cantidad' => 'required|max:3|unique:libro,cantidad,' . $this->route('id'),
            'editorial' => 'max:50|unique:libro,editorial,' . $this->route('id'),
            'foto' => 'max:100|unique:libro,foto,' . $this->route('id'),
        ];
    }
}
