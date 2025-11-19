<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
    // Definir permisos
    Gate::define('manage-projects', function ($user) {
        return $user->role === 'admin';
    });
    
    Gate::define('view-projects', function ($user) {
        return in_array($user->role, ['admin', 'viewer']);
    });
}
}
