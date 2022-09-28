<?php

namespace App\Models\Role;

use Carbon\Carbon;
use App\Models\BaseModel;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ATTRIBUTES
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * RELATIONS
 * @property Collection|iterable<int, User> users
 */
class Role extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name'];

    /***********************************************************************
     *                                                                     *
     *                              RELATIONS                              *
     *                                                                     *
     **********************************************************************/

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
