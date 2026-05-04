<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Gate: only admins can access admin-only features
        Gate::define('admin-only', fn (User $user) => $user->isAdmin());

        // Gate: admins and staff can access operational features
        Gate::define('staff-access', fn (User $user) => $user->isAdmin() || $user->isStaff());
    }
}
