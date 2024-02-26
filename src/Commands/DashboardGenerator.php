<?php

namespace Niraj\CrudStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

class DashboardGenerator extends Command
{
    //hides the command from terminal
    protected $hidden = true;

    protected $signature = "gen:dashboard";

    protected $description = 'Laravel Dashboard based on Sneat Admin Panel';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Process::run('composer require laravel/ui');
        Process::run('php artisan ui bootstrap --auth');
        Process::run('composer require proengsoft/laravel-jsvalidation');
        Process::run('php artisan vendor:publish --provider="Proengsoft\JsValidation\JsValidationServiceProvider"');

        $this->publishBladeAssets();
        $this->publishDashboardAssets();
        $this->publishTraits();

        $this->info('Laravel Auth UI scaffolding replaced successfully.');
        $this->info('proengsoft/laravel-jsvalidation package added.');
        $this->comment('Please execute the "npm install && npm run dev" command to build your assets.');
        $this->comment('Then run npm run build to compile them.');
    }

    protected function publishBladeAssets()
    {
        $this->confirmDirectoryExists("/views/_dasboard");

        \File::copyDirectory(__DIR__.'/../dashboard/auth', resource_path("/views/auth"));
        \File::copyDirectory(__DIR__.'/../dashboard/layouts', resource_path("/views/layouts"));
        \File::copyDirectory(__DIR__.'/../dashboard/_helpers', resource_path("/views/_helpers"));
        \File::copyDirectory(__DIR__.'/../components', resource_path("/views/components"));
        copy(__DIR__.'/../dashboard/home.blade.php', resource_path("/views/home.blade.php"));
    }

    protected function publishDashboardAssets()
    {
        $this->confirmDirectoryExists("/public/assets");
        $this->confirmDirectoryExists("/public/assets/css");
        $this->confirmDirectoryExists("/public/assets/img");
        $this->confirmDirectoryExists("/public/assets/js");
        $this->confirmDirectoryExists("/public/assets/vendor");

        \File::copyDirectory(__DIR__.'/../assets/css', base_path("/public/assets/css"));
        \File::copyDirectory(__DIR__.'/../assets/img', base_path("/public/assets/img"));
        \File::copyDirectory(__DIR__.'/../assets/js', base_path("/public/assets/js"));
        \File::copyDirectory(__DIR__.'/../assets/vendor', base_path("/public/assets/vendor"));
    }

    protected function confirmDirectoryExists(string $path)
    {
        if (!file_exists($path = base_path($path))) {
            mkdir($path, 0777, true);
        }
    }

    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (!file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    protected function publishTraits()
    {
        if (!file_exists($path = app_path("/Traits"))) {
            mkdir($path, 0777, true);
        }

        \File::copyDirectory(__DIR__.'/../stubs/Traits', app_path("/Traits"));
    }
}
