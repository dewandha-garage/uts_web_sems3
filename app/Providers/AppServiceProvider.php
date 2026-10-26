<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useTailwind();

        Gate::define('manage-users', fn (User $user) => $user->isOwner());
        Gate::define('cancel-transaction', fn (User $user) => $user->isOwner());
        Gate::define('manage-master', fn (User $user) => $user->hasRole([User::ROLE_OWNER, User::ROLE_ADMIN]));
        Gate::define('manage-purchases', fn (User $user) => $user->hasRole([User::ROLE_OWNER, User::ROLE_ADMIN]));
        Gate::define('manage-sales', fn (User $user) => $user->hasRole([User::ROLE_OWNER, User::ROLE_ADMIN]));
        Gate::define('access-backoffice', fn (User $user) => $user->hasRole([User::ROLE_OWNER, User::ROLE_ADMIN]));
    }
}
