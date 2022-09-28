<?php

namespace App\Models\User;

use Carbon\Carbon;
use App\Models\Role\Role;
use App\Models\Role\RoleName;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Services\QueryBuilder\HasQueryBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * ATTRIBUTES
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property string|null $token
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $role_id
 * RELATIONS
 * @property Role $role
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use HasQueryBuilder;

    public static array $requiredRelationsMatch = [];

    public static array $allowedIncludes = ['role'];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'token',
        'password',
        'role_id',
    ];

    protected $hidden = ['password', 'remember_token', 'token'];

    protected $casts = ['email_verified_at' => 'datetime'];

    /***********************************************************************
     *                                                                     *
     *                              RELATIONS                              *
     *                                                                     *
     **********************************************************************/

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /***********************************************************************
     *                                                                     *
     *                              FUNCTIONS                              *
     *                                                                     *
     **********************************************************************/

    public static function getRequiredRelationsMatch(): array
    {
        return self::$requiredRelationsMatch;
    }

    public static function getAllowedIncludes(): array
    {
        return self::$allowedIncludes;
    }

    public function hasRole(RoleName ...$roles): bool
    {
        $roleNames = [];

        foreach ($roles as $role) {
            $roleNames[] = $role->value();
        }

        return in_array($this->role->name, $roleNames);
    }
}
