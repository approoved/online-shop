<?php

namespace App\Providers;

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
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
