<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route; // Tambahkan ini untuk menggunakan Route

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
        // Memastikan bahwa file routes/api.php dibaca dengan benar
        $this->loadRoutesFrom(base_path('routes/api.php')); // Mengganti dengan loadRoutesFrom

        // Jika ingin menambahkan middleware untuk semua route
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));
    }
}