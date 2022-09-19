<?php

namespace App\Models\ProductDetail;

use Carbon\Carbon;
use App\Models\BaseModel;
use App\Models\FieldType\FieldTypeName;
use App\Models\ProductField\ProductField;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ATTRIBUTES
 * @property int $id
 * @property string|Carbon $value
 * @property int $product_id
 * @property int $product_field_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * RELATIONS
 * @property ProductField $field
 */
class ProductDetail extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'value',
        'product_id',
        'product_field_id',
    ];

    protected $with = [
        'field.type'
    ];

    public static array $allowedIncludes = [
        'field'
    ];

    /***********************************************************************
     *                                                                     *
     *                              RELATIONS                              *
     *                                                                     *
     **********************************************************************/

    public function field(): BelongsTo
    {
        return $this->belongsTo(
            ProductField::class,
            'product_field_id',
            'id'
        );
    }

    /***********************************************************************
     *                                                                     *
     *                               SCOPES                                *
     *                                                                     *
     **********************************************************************/

    /***********************************************************************
     *                                                                     *
     *                               SETTERS                               *
     *                                                                     *
     **********************************************************************/

    /***********************************************************************
     *                                                                     *
     *                               GETTERS                               *
     *                                                                     *
     **********************************************************************/

    public function getValueAttribute($value): Carbon|string
    {
        if ($this->field->hasType(FieldTypeName::Date)) {
            return Carbon::parse($value);
        }

        return $value;
    }

    /***********************************************************************
     *                                                                     *
     *                              FUNCTIONS                              *
     *                                                                     *
     **********************************************************************/
}
