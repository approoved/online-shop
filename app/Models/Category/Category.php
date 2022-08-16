<?php

namespace App\Models\Category;

use Carbon\Carbon;
use App\Models\Product\Product;
use Franzose\ClosureTable\Models\Entity;
use App\Models\ProductFilter\ProductFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Franzose\ClosureTable\Extensions\Collection as FranzoseCollection;

/**
 * PROPERTIES
 * @property int id
 * @property string name
 * @property int|null parent_id
 * @property int position
 * @property Carbon created_at
 * @property Carbon updated_at
 * RELATIONS
 * @property Collection|null children
 * @property Category|null parent
 * @property Collection|null products
 * @property Collection<int, ProductFilter>|null filters
 */
class Category extends Entity
{
    use HasFactory;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'parent_id',
    ];

    /**
     * @var string
     */
    protected $table = 'categories';

    /**
     * @var CategoryClosure
     */
    protected $closure = CategoryClosure::class;

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function filters(): HasMany
    {
        return $this->hasMany(ProductFilter::class, 'category_id', 'id');
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
