<?php

namespace App\Providers;

use App\Rules\ValuesIn;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Validator::extend(
            'values_in',
            function (string $attribute, mixed $value, array $parameters) {
                foreach (explode(',', $value) as $element) {
                    if (! in_array($element, $parameters)) {
                        return false;
                    }
                }

                return true;
            },
            'The \':attribute\' field must have allowed value(s).'
        );
    }
}
