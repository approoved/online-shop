<?php

namespace App\Services\QueryBuilder;

use Spatie\QueryBuilder\QueryBuilder;

trait HasQueryBuilder
{
    public static function getSearchQuery(): QueryBuilder
    {
        return QueryBuilder::for(static::class)
            ->allowedIncludes(static::getAllowedIncludes())
            ->with(static::getRequiredRelations());
    }

    private static function getRequiredRelations(): array
    {
        $requiredRelations = [];

        $includes = explode(',', request()->include) ?? [];
        $appends = explode(',', request()->append) ?? [];

        $requestedData = array_merge($includes, $appends);

        foreach ($requestedData as $element) {
            if (array_key_exists($element, static::getRequiredRelationsMatch())) {
                $requiredRelations = array_merge($requiredRelations, static::getRequiredRelationsMatch()[$element]);
            }
        }

        return $requiredRelations;
    }

    abstract private static function getAllowedIncludes(): array;

    abstract private static function getRequiredRelationsMatch(): array;
}
