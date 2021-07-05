<?php

namespace Ryan\LineKit;

use Illuminate\Support\ServiceProvider;
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
        Route::group([
            // 'domain' => 'api' . config('app.url'), // don't call `env` outside of configs
            // 'namespace' => $this->namespace,
            'prefix' => 'api/backend',
            // 'middleware' => 'api',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api-backend.php');
        });
    }
}
