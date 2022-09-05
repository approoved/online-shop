<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;

class ArrayWithIntegerIndices implements Rule
{
    /**
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        if (! is_array($value)) {
            return false;
        }

        foreach ($value as $key => $val) {
            if (! is_int($key)) {
                return false;
            }
        }

        return true;
    }

    public function message(): string
    {
        return 'The :attribute must be an array with product fields ids indices';
    }
}
