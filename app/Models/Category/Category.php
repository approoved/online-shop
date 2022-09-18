<?php

namespace App\Models\Category;

use Carbon\Carbon;
use App\Models\Product\Product;
use Franzose\ClosureTable\Models\Entity;
use App\Models\ProductField\ProductField;
use App\Models\ProductFilter\ProductFilter;
use App\Models\ProductDetail\ProductDetail;
use Illuminate\Database\Eloquent\Collection;
use App\Services\QueryBuilder\HasQueryBuilder;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Franzose\ClosureTable\Extensions\Collection as FranzoseCollection;

/**
 * ATTRIBUTES
 * @property int $id
 * @property string $name
 * @property int|null $parent_id
 * @property int $position
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * RELATIONS
 * @property Collection|null $children
 * @property Category|null $parent
 * @property Collection|null $products
 * @property Collection<int, ProductFilter>|null $filters
 * @property Collection<int, ProductField>|null $fields
 */
class Category extends Entity
{
    use HasFactory;
    use HasQueryBuilder;
    use HasRelationships;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'parent_id',
    ];

    protected $hidden = [
        'parent_id',
        'position',
    ];

    protected $table = 'categories';

    protected $closure = CategoryClosure::class;

    public static array $allowedIncludes = [
        'products'
    ];

    public static array $requiredRelationsMatch = [];

    /***********************************************************************
     *                                                                     *
     *                              RELATIONS                              *
     *                                                                     *
     **********************************************************************/

    public function children(): HasMany
    {
        return $this->hasMany(
            Category::class,
            'parent_id',
            'id'
        );
    }

    public function products(): HasMany
    {
        return $this->hasMany(
            Product::class,
            'category_id',
            'id'
        );
    }

    public function filters(): HasMany
    {
        return $this->hasMany(
            ProductFilter::class,
            'category_id',
            'id'
        );
    }

    public function fields(): HasManyDeep
    {
        return $this->hasManyDeep(
            ProductField::class,
            [Product::class, ProductDetail::class],
            [
                'category_id',
                'product_id',
                'id'
            ],
            [
                'id',
                'id',
                'product_field_id'
            ]
        )->distinct();
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

    /***********************************************************************
     *                                                                     *
     *                              FUNCTIONS                              *
     *                                                                     *
     **********************************************************************/

    public static function getAllowedIncludes(): array
    {
        return self::$allowedIncludes;
    }

    public static function getRequiredRelationsMatch(): array
    {
        return self::$requiredRelationsMatch;
    }

    public function hasProducts(): bool
    {
        return count($this->products) > 0;
    }

    public function appendAncestors(): Category
    {
        /** @var FranzoseCollection $collection */
        $collection = $this->ancestorsWithSelf()->get();

        /** @var Category[] $result */
        $result = [];

        /** @var Category $item */
        foreach ($collection as $item) {
            $result[$item->parent_id] = $item;
        }

        foreach ($collection as $item) {
            if (key_exists($item->id, $result)) {
                $result[$item->id]->parent = $item;
            }
        }

        return $result[$this->parent_id];
    }
}
