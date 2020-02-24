<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Myprofile extends FormRequest
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
            'name' => 'string|max:255',
                'last_name' => 'string|min:2max:255',
                'email' => 'string|email|max:255|unique:users',
                'password' => 'string|min:8|confirmed',
                'image' => 'image|dimensions:min_width=100,min_height=100',
        ];
    }
}
