<?php

namespace App\Http\Requests\Auth;

use App\Rules\IsValidPassword;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisterAuthRequest extends FormRequest
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
      // Need pass the attribute password_confirmation
      $rules = [
        'name' => 'required',
        'first_last_name' => 'required',
        'second_last_name' => 'required',
        'dni' => 'required',
        'role_id' => 'required',
        'email' => 'required|unique:users|email',
        'password' => [
          'required',
          'confirmed',
          Password::min(8),
          new IsValidPassword(),
        ],
      ];
  
      return $rules;
    }
}
