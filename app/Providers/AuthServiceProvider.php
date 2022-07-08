<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Role\Role;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Laravel\Passport\Passport;
use App\Policies\CategoryPolicy;
use App\Models\Category\Category;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Category::class => CategoryPolicy::class,
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
