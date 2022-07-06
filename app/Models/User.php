<?php

namespace App\Models;

use App\Models\Role\RoleName;
use Carbon\Carbon;
use App\Models\Role\Role;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int id
 * @property string first_name
 * @property string last_name
 * @property string email
 * @property string password
 * @property string remember_token
 * @property string|null token
 * @property Carbon|null email_verified_at
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property Role role
 * @property int role_id
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'token',
        'password',
        'role_id',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(RoleName $role): bool
    {
        return $this->role->name === $role->value();
    }
}
