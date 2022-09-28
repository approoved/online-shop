<?php

namespace App\Models\FieldType;

use Carbon\Carbon;
use RuntimeException;
use App\Models\BaseModel;
use App\Exceptions\InvalidDataTypeException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ATTRIBUTES
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class FieldType extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name'];

    /***********************************************************************
     *                                                                     *
     *                              FUNCTIONS                              *
     *                                                                     *
     **********************************************************************/

    /**
     * @throws InvalidDataTypeException
     */
    public function validateDataType(mixed $value): void
    {
        switch($this->name) {
            case FieldTypeName::Text->value():
                ! is_array($value) ?: throw new InvalidDataTypeException(
                    'Value must be a string.'
                );
                break;

            case FieldTypeName::Integer->value():
                is_int($value) ?: throw new InvalidDataTypeException(
                    'Value must be an integer.'
                );
                break;

            case FieldTypeName::Float->value():
                is_float($value) ?: throw new InvalidDataTypeException(
                    'Value must be a floating-point number.'
                );
                break;

            case FieldTypeName::Date->value():
                Carbon::canBeCreatedFromFormat($value, 'Y-m-d H:i:s')
                    ?: throw new InvalidDataTypeException(
                        'Value must be a datetime in format: Y-m-d H:i:s.'
                    );
                break;

            default:
                throw new RuntimeException(
                    'Validation for %s field type is not configurated yet.',
                    $this->name
                );
        }
    }
}
