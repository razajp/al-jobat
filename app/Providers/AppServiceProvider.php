<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->singleton('company', function () {
            return (object) [
                'name' => 'Al Jobat',
                'owner_name' => 'Zubair',
                'logo' => 'company_logo.png',
                'phone_number' => '021-36907419 | 0321-8692696',
                'date'  => '12-12-2012',
                'city' => 'Karachi',
                'address' => 'Plot DP-19, Sec. 12-C, Ind. Area, North Karachi',
            ];
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View::share('authLayout', 'table');
    }
}
