<?php

namespace App\Http\Requests\ValidationFields;

use RuntimeException;

class IncludeRules
{
    public static function getRules(string $model): array
    {
        if (! method_exists($model, 'getAllowedIncludes')) {
            throw new RuntimeException(
                sprintf(
                    'Method getAllowedIncludes() not declared. Class - %s.',
                    $model
                )
            );
        }

        return [
            'include' => [
                'sometimes',
                'string',
                'values_in:' . implode(',', $model::getAllowedIncludes()),
            ],
        ];
    }
}
