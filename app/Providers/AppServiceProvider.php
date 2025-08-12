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

        app()->singleton('article', function () {
            return (object) [
                'categories' => [
                    '1_pc' => ['text' => '1 Pc'],
                    '1_pc_inner' => ['text' => '1 Pc + Inner'],
                    '1_pc_koti' => ['text' => '1 Pc + Koti'],
                    '2_pc' => ['text' => '2 Pc'],
                    '3_pc' => ['text' => '3 Pc'],
                ],
                'seasons' => [
                    'half' => ['text' => 'Half'],
                    'full' => ['text' => 'Full'],
                    'winter' => ['text' => 'Winter'],
                ],
                'sizes' => [
                    '1_2' => ['text' => '1-2'],
                    'sml' => ['text' => 'SML'],
                    '18_20_22' => ['text' => '18-20-22'],
                    '18_20_22_24' => ['text' => '18-20-22-24'],
                    '20_22_24' => ['text' => '20-22-24'],
                    '24_26_28' => ['text' => '24-26-28'],
                ],
                'parts' => [
                    '1_pc_inner_half' => ['shirt', 'inner'],
                    '1_pc_koti_half' => ['shirt', 'koti'],
                    '2_pc_half' => ['shirt', 'neker'],
                    '3_pc_half' => ['koti', 'inner', 'neker'],
                    '1_pc_inner_full' => ['shirt', 'inner'],
                    '1_pc_koti_full' => ['shirt', 'koti'],
                    '2_pc_full' => ['shirt', 'trouser'],
                    '3_pc_full' => ['koti', 'inner', 'neker'],
                    '1_pc_inner_winter' => ['shirt', 'inner'],
                    '1_pc_koti_winter' => ['shirt', 'koti'],
                    '2_pc_winter' => ['shirt', 'trouser'],
                    '3_pc_winter' => ['koti', 'inner', 'trouser'],
                ],
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
