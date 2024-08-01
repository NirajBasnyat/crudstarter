<?php

namespace Niraj\CrudStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Niraj\CrudStarter\Traits\generalHelperTrait;

class DashboardGenerator extends Command
{
    use generalHelperTrait;

    //hides the command from terminal
    protected $hidden = true;

    protected $signature = "gen:dashboard";

    protected $description = 'Laravel Dashboard based on Sneat Admin Panel';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        //check laravel version
        if($this->getInstalledLaravelVersion() > 10) {
            copy(__DIR__.'/../replacementFiles/controller.php', app_path("/Http/Controllers/Controller.php"));
            copy(__DIR__.'/../replacementFiles/welcome.blade.php', resource_path("views/welcome.blade.php"));
        }

        $this->info('Please wait while the dashboard is being generated...');

        Process::run('composer require laravel/ui');
        Process::run('php artisan ui bootstrap --auth');
        Process::run('composer require proengsoft/laravel-jsvalidation');
        Process::run('php artisan vendor:publish --provider="Proengsoft\JsValidation\JsValidationServiceProvider"');

        $this->publishBladeAssets();
        $this->publishDashboardAssets();
        $this->publishTraits();

        $this->info('Laravel Auth UI scaffolding replaced successfully.');
        $this->info('proengsoft/laravel-jsvalidation package added.');
        $this->warn('Please execute the "npm install" command in your terminal.');
        $this->warn('Then run "npm run build" to compile assets.');
    }

    protected function publishBladeAssets(): void
    {
        $this->confirmDirectoryExists("/views/_dasboard");

        \File::copyDirectory(__DIR__.'/../dashboard/auth', resource_path("/views/auth"));
        \File::copyDirectory(__DIR__.'/../dashboard/layouts', resource_path("/views/layouts"));
        \File::copyDirectory(__DIR__.'/../dashboard/_helpers', resource_path("/views/_helpers"));
        \File::copyDirectory(__DIR__.'/../components', resource_path("/views/components"));
        copy(__DIR__.'/../dashboard/home.blade.php', resource_path("/views/home.blade.php"));
    }

    protected function publishDashboardAssets(): void
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

    protected function confirmDirectoryExists(string $path): void
    {
        if (!file_exists($path = base_path($path))) {
            mkdir($path, 0777, true);
        }
    }

    protected function publishTraits(): void
    {
        if (!file_exists($path = app_path("/Traits"))) {
            mkdir($path, 0777, true);
        }

        \File::copyDirectory(__DIR__.'/../stubs/Traits', app_path("/Traits"));
    }
}
