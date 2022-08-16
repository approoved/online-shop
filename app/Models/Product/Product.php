<?php

namespace App\Models\Product;

use Carbon\Carbon;
use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Model;
use App\Http\Services\Elasticsearch\Searchable;
use App\Http\Services\Elasticsearch\SearchableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * PROPERTIES
 * @property int id
 * @property string sku
 * @property string name
 * @property int category_id
 * @property int price
 * @property int quantity
 * @property array details
 * @property Carbon created_at
 * @property Carbon updated_at
 * RELATIONS
 * @property Category category
 */
class Product extends Model implements Searchable
{
    use HasFactory;
    use SearchableTrait;

    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'price',
        'quantity',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public const ELASTIC_INDEX = 'products';

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function toSearchArray(): array
    {
        return $this->toArray();
    }
}
