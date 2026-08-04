<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;
use App\Models\User;

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
        // Use Bootstrap 5 pagination layout across the application
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Super Admin bypass: Admin automatically has all permissions
        Gate::before(function (User $user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });

        // Register dynamic gates for all permissions in the database
        if (!app()->runningInConsole()) {
            try {
                $permissions = Permission::all();
                foreach ($permissions as $permission) {
                    Gate::define($permission->name, function (User $user) use ($permission) {
                        return $user->hasPermission($permission->name);
                    });
                }
            } catch (\Exception $e) {
                // Prevent connection errors during migration / configuration cache
            }
        }
    }
}
