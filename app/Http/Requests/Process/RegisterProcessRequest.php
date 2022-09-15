<?php

namespace App\Http\Requests\Process;

use Illuminate\Foundation\Http\FormRequest;

class RegisterProcessRequest extends FormRequest
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
            'se_oid' => 'required',
            'se_name' => 'required',
            'name' => 'required|min:10',
            'visible' => 'required',
        ];
    }
}
