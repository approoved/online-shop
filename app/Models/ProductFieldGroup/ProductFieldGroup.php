<?php

namespace App\Models\ProductFieldGroup;

use Carbon\Carbon;
use RuntimeException;
use App\Models\BaseModel;
use App\Models\Product\Product;
use App\Models\ProductField\ProductField;
use App\Models\ProductDetail\ProductDetail;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\InvalidInputDataException;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ATTRIBUTES
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * RELATIONS
 * @property Collection<int, ProductField>|null $fields
 * @property Collection<int, Product>|null $products
 */
class ProductFieldGroup extends BaseModel
{
    use HasFactory;
    use HasRelationships;

    protected $fillable = ['name'];

    public static array $allowedIncludes = ['fields.type'];

    /***********************************************************************
     *                                                                     *
     *                              RELATIONS                              *
     *                                                                     *
     **********************************************************************/

    public function fields(): HasMany
    {
        return $this->hasMany(
            ProductField::class,
            'product_field_group_id',
            'id'
        );
    }

    public function products(): HasManyDeep
    {
        return $this->hasManyDeep(
            Product::class,
            [ProductField::class, ProductDetail::class],
            [
                'product_field_group_id',
                'product_field_id',
                'id',
            ],
            [
                'id',
                'id',
                'product_id',
            ]
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

    /**
     * @throws InvalidInputDataException
     */
    public function setNameAttribute(string $value): void
    {
        if ($this->name) {
            throw new RuntimeException(
                'Group name updating is restricted.',
            );
        }

        $exists = ProductFieldGroup::query()
            ->where('name', $value)
            ->first();

        if ($exists) {
            throw new InvalidInputDataException(
                'The name has already been taken. Delete existing group first.'
            );
        }

        $this->attributes['name'] = $value;
    }

    /***********************************************************************
     *                                                                     *
     *                               GETTERS                               *
     *                                                                     *
     **********************************************************************/

    /***********************************************************************
     *                                                                     *
     *                              FUNCTIONS                              *
     *                                                                     *
     **********************************************************************/

    public function hasProducts(): bool
    {
        return $this->products()->count();
    }
}
