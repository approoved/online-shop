<?php

namespace App\Models\ProductField;

use Carbon\Carbon;
use http\Exception\RuntimeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int id
 * @property string name
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */
class FieldType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function acceptDataType(mixed $value): bool
    {
        return match ($this->name) {
            FieldTypeName::text->value() => ! is_array($value),
            FieldTypeName::integer->value() => is_int($value),
            FieldTypeName::float->value() => is_float($value),
            FieldTypeName::date->value() => Carbon::canBeCreatedFromFormat(
                $value,
                'Y-m-d h:i:s'
            ),

            default => throw new RuntimeException(
                'Validation for %s field type is not configurated yet.',
                $this->name
            )
        };
    }
}
