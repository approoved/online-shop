<?php

namespace App\Models\ProductFilter;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int id
 * @property string name
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */
class ProductFilterType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}
