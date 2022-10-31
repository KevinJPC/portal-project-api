<?php

namespace App\Http\Requests\Process;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcessRequest extends FormRequest
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
<<<<<<< HEAD
            'name' => 'required|min:10',
=======
            'name' => [
                'required',
                'min:10',
                'unique:processes,name,' . $this->route('process')->id,
            ],
>>>>>>> 09e8246839c7d6bd0bbb12a88c21480c4dca3ff3
            'visible' => 'required',
            'roles' => 'required',
        ];
    }
}