<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MyprofileRequest extends FormRequest
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

     // je pourrais de cette maniere injecter cette classe qui
     // sert de validateur a la méthode de mon controlleur
    public function rules()
    {
        return [
          'name' => ['required', 'string', 'min:3,max:255'],
          'last_name' => ['required', 'string', 'min:3,max:255'],
          'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
          'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
