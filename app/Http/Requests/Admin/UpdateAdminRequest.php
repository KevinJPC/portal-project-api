<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateAdminRequest extends FormRequest
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
        return [
            'name' => 'required|max:60',
            'first_last_name' => 'required|max:60',
            'second_last_name' => 'required|max:60',
            'email' => [
                'required',
                'email:rfc,dns',
                'max:60',
                'unique:users,email,' . $this->route('user')->id,
            ],
        ];
    }
}
