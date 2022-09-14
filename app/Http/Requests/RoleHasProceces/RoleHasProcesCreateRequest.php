<?php

namespace App\Http\Requests\RoleHasProceces;

use Illuminate\Foundation\Http\FormRequest;

class RoleHasProcesCreateRequest extends FormRequest
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
            'process_id' => 'required'
        ];
    }
}
