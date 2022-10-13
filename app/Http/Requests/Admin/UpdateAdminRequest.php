<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

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
                'email',
                'max:60',
                'unique:users,email,' . $this->route('user')->id,
            ],
        ];
    }
}
