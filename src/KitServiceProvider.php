<?php

namespace Ryan\LineKit;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class KitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->routes(function () {
            Route::prefix('api/backend')
                ->middleware('api')
                ->group(__DIR__ . '/../routes/api-backend.php');
        });
    }
}
