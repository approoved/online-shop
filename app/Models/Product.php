<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int id
 * @property string sku
 * @property string name
 * @property int category_id
 * @property int price
 * @property int quantity
 * @property array details
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Category category
 */
class Product extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'price',
        'quantity',
        'details',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'details' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
