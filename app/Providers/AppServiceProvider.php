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
                'phone_number' => '0312-1234567',
                'date'  => '12-12-2012',
                'city' => 'Karachi',
                'address' => '6-B, Industrial Area',
            ];
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::share('authLayout', Auth::check() ? Auth::user()->layout : 'grid');
        // View::share('authLayout', 'table');
    }
}
