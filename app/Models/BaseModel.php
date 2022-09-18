<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\QueryBuilder\HasQueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class BaseModel extends Model
{
    use HasFactory;
    use HasQueryBuilder;

    public static array $allowedIncludes = [];

    public static array $requiredRelationsMatch = [];

    public static function getAllowedIncludes(): array
    {
        return static::$allowedIncludes;
    }

    public static function getRequiredRelationsMatch(): array
    {
        return static::$requiredRelationsMatch;
    }
}
