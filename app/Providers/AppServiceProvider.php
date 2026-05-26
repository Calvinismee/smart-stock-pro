<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\Gate::define('viewPulse', function (\App\Models\User $user) {
            return $user->role === 'admin';
        });

        \Illuminate\Validation\Rules\Password::defaults(function () {
            $rule = \Illuminate\Validation\Rules\Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols();

            return app()->isProduction() ? $rule->uncompromised() : $rule;
        });
    }
}
