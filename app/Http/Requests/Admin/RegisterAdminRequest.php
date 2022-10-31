<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IsValidPassword;
use Illuminate\Validation\Rules\Password;

class RegisterAdminRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'dni' => 'required|max:20|regex:/^[0-9]+$/',
            'name' => 'required|max:60',
            'first_last_name' => 'required|max:60',
            'second_last_name' => 'required|max:60',
            'email' => 'required|unique:users|email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        return $rules;
    }
}
