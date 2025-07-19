<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Admin Gate
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        // Mechanic Gate
        Gate::define('mechanic', function ($user) {
            return $user->role === 'mechanic';
        });

        // Customer Gate
        Gate::define('customer', function ($user) {
            return $user->role === 'customer';
        });

        // Admin or Mechanic Gate
        Gate::define('admin-or-mechanic', function ($user) {
            return in_array($user->role, ['admin', 'mechanic']);
        });
    }
}