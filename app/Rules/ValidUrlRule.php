<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidUrlRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $fail('حقل :attribute يجب أن يكون رابطاً صحيحاً وموثوقاً.');
        }
    }
}