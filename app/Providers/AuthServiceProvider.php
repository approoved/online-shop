<?php

namespace App\Providers;

use App\Models\Role\Role;
use App\Models\User\User;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Laravel\Passport\Passport;
use App\Models\Product\Product;
use App\Policies\ProductPolicy;
use App\Policies\CategoryPolicy;
use App\Models\Category\Category;
use App\Policies\ProductFieldPolicy;
use App\Policies\ProductFilterPolicy;
use App\Models\ProductField\ProductField;
use App\Policies\ProductFieldGroupPolicy;
use App\Policies\ProductFilterValuePolicy;
use App\Models\ProductFilter\ProductFilter;
use App\Models\ProductFieldGroup\ProductFieldGroup;
use App\Models\ProductFilterValue\ProductFilterValue;
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
        ProductFieldGroup::class => ProductFieldGroupPolicy::class,
        ProductField::class => ProductFieldPolicy::class,
        Product::class =>ProductPolicy::class,
        ProductFilter::class => ProductFilterPolicy::class,
        ProductFilterValue::class => ProductFilterValuePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
