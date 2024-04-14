<?php

namespace Niraj\CrudStarter;

use Illuminate\Support\ServiceProvider;
use Niraj\CrudStarter\Commands\ApiGenerator;
use Niraj\CrudStarter\Commands\CrudGenerator;
use Niraj\CrudStarter\Commands\DashboardGenerator;
use Niraj\CrudStarter\Commands\DeleteApi;
use Niraj\CrudStarter\Commands\DeleteCrud;

class CrudStarterServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DashboardGenerator::class,
                CrudGenerator::class,
                DeleteCrud::class,
                ApiGenerator::class,
                DeleteApi::class,
            ]);

            $this->publishes([
                __DIR__.'/stubs' => base_path("/resources/crud-stub"),
            ], 'crud-stub');

            $this->publishes([
                __DIR__.'/config/crudstarter.php' => config_path('crudstarter.php'),
            ], 'crudstarter-config');
        }
    }

    public function register()
    {

    }
}
