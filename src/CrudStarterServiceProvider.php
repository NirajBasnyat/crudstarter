<?php

namespace Niraj\CrudStarter;

use Niraj\CrudStarter\Commands\ApiGenerator;
use Niraj\CrudStarter\Commands\CrudGenerator;
use Illuminate\Support\ServiceProvider;

class CrudStarterServiceProvider extends ServiceProvider
{

    protected $defer = false;

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ApiGenerator::class,
                CrudGenerator::class,
            ]);

            $this->publishes([
                __DIR__.'/stubs' => base_path("/resources/crud-stub"),
            ], 'crud-stub');
        }
    }

    public function register()
    {

    }
}