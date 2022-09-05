<?php

namespace App\Models\ProductField;

use Carbon\Carbon;
use RuntimeException;
use App\Models\BaseModel;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * PROPERTIES
 * @property int id
 * @property string name
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * RELATIONS
 * @property Collection<int, ProductField>|null fields
 * @property Collection<int, Product>|null products
 */
class ProductFieldGroup extends BaseModel
{
    use HasFactory;
    use HasRelationships;

    protected $fillable = ['name'];

    public static array $allowedIncludes = [
        'fields'
    ];

    public function setNameAttribute(string $value): void
    {
        if ($this->name) {
            throw new RuntimeException(
                'Group name updating is restricted.',
                Response::HTTP_CONFLICT
            );
        }

        $exists = ProductFieldGroup::query()
            ->where('name', $value)
            ->first();

        if ($exists) {
            throw new HttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                'The name has already been taken.'
            );
        }

        $this->attributes['name'] = $value;
    }

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
            [ProductField::class, 'field_product'],
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

    public function hasProducts(): bool
    {
        return $this->products()->count();
    }
}
