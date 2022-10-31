<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class ModifyRoleRequest extends FormRequest
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
            'name' => 'required|min:5',
            'name_slug' => 
            ['required',
            'unique:roles,name_slug,'. $this->route('role')->id,],
            'description' => 'min:5',
        ];
    }
}