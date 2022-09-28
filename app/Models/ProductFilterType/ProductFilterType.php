<?php

namespace App\Models\ProductFilterType;

use Carbon\Carbon;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ATTRIBUTES
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ProductFilterType extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name'];
}
