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
        app()->singleton('company', function () {
            return (object) [
                'name' => 'Al Jobat',
                'owner_name' => 'Zubair',
                'company_logo' => 'company_logo.png',
                'phone' => '+92 300 1234567',
                'date'  => '12-12-2012',
                'address' => 'Al Jobat 6-B, Industrial Area',
            ];
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
