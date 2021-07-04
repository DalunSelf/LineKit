<?php

namespace Ryan\LineKit\Tests;

use Illuminate\Support\Facades\Artisan;
use Ryan\LineKit\KitServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->artisan('migrate', ['--database' => 'testing'])->run();
    }

    protected function getPackageProviders($app)
    {
        return [
            KitServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',

            // 'driver' => 'mysql',
            // 'host' => '127.0.0.1',
            // 'port' => '3306',
            // 'database' => 'full-bot',
            // 'username' => 'root',
            // 'password' => '',
        ]);
    }
}
