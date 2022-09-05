<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\QueryBuilder\HasQueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class BaseModel extends Model
{
    use HasFactory;
    use HasQueryBuilder;

    protected static array $allowedIncludes = [];

    protected static array $requiredRelationsMatch = [];

    private static function getAllowedIncludes(): array
    {
        return static::$allowedIncludes;
    }

    private static function getRequiredRelationsMatch(): array
    {
        return static::$requiredRelationsMatch;
    }
}
