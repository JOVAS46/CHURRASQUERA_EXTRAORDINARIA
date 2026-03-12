<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Role;
use App\Policies\RolePolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar políticas
        Gate::policy(Role::class, RolePolicy::class);
    }
}
