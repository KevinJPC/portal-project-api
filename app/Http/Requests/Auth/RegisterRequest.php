<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'email' => Str::lower($this->email),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        //lower email

        $rules = [
            'name' => 'required',
            'first_last_name' => 'required',
            'second_last_name' => 'required',
            'dni' => 'required',
            'role_id' => 'required|exists:roles,id',
            'email' => 'required|unique:users|email:rfc,dns',
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        return $rules;
    }
}
