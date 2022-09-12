<?php

namespace App\Rules;

use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\InvokableRule;

class IsValidPassword implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        //
        if (!Str::match('/\d/', $value)) {
            $fail(':attribute debe tener números.');
        }

        if (!Str::match('/[a-zA-Z]/', $value)) {
            $fail(':attribute debe tener letras.');
        }

        if (!Str::match('/[A-Z].*[a-z]|[a-z].*[A-Z]/', $value)) {
            $fail(':attribute debe tener mayúsculas y minúsculas.');
        }

        if (!Str::match('/[^a-zA-Z\d]/', $value)) {
            $fail(':attribute debe tener carácteres especiales.');
        }
    }
}
