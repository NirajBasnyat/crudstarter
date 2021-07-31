<?php

namespace Niraj\CrudStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudGenerator extends Command
{
    protected $signature = "gen:crud {name}";

    protected $description = 'Generates Basic Laravel Crud :)';

    public function __construct()
    {
        parent::__construct();

        $this->stub_path = base_path('vendor/niraj/crudstarter/src/stubs');
    }

    public function handle()
    {
        $name = $this->argument('name');
        $this->model_stub($name);
        $this->request_stub($name);
        $this->factory_stub($name);

        //define variable
        $this->snake_case = Str::snake($name);
        $this->snake_case_plural = Str::plural(Str::snake($name));
        $this->kebab_case_plural = Str::plural(Str::kebab($name));

        //add in Database seederFile
        $current_contents = file_get_contents(base_path("database/seeders/DatabaseSeeder.php"));
        $factory_name = "\\App\Models\\" . $name . "::factory(5)->create();";
        $replacement = str_replace('//here', $factory_name, $current_contents);
        file_put_contents(base_path("database/seeders/DatabaseSeeder.php"), $replacement);

        //make migration
        Artisan::call('make:migration create_' . $this->snake_case_plural . '_table --create=' . $this->snake_case_plural);

        if ($this->confirm('Do you want to add controllers in specific folder ?')) {

            $folder_name = $this->ask('Enter the Folder Name');

            //add named resource controller in web.php
            File::append(
                base_path('routes/web.php'),
                'Route::resource(\'' . $this->kebab_case_plural . "',\\App\Http\Controllers\\" . $folder_name . "\\" . $name . "Controller::class);" . PHP_EOL
            );

            $this->named_controller_stub($name, $folder_name);

            $this->named_blade_stub($name, $folder_name);
        } else {
            //add resource controller in web.php
            File::append(
                base_path('routes/web.php'),
                'Route::resource(\'' . $this->kebab_case_plural . "',\\App\Http\Controllers\\" . $name . "Controller::class);" . PHP_EOL
            );

            $this->controller_stub($name);

            $this->blade_stub($name);
        }

        //to generate test
        if ($this->confirm('Do you wish to generate Test?')) {
            $this->feature_test_stub($name);
            $this->line($name . ' Test was generated successfully !!');
        }

        $this->info($name . ' crud was generated successfully !!');
    }

    protected function getStub($type)
    {
        return file_get_contents("$this->stub_path/$type.stub");
    }

    protected function getBladeStub($type)
    {
        return file_get_contents("$this->stub_path/blade/$type.stub");
    }

    protected function model_stub($name)
    {
        //gives model with replaced placeholder
        $template = str_replace(
            ['{{modelName}}'],
            [$name], //name comes from command
            $this->getStub('model')
        );

        //create file dir if it doesnot exist
        if (!file_exists($path = app_path("/Models"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Models/{$name}.php"), $template);
    }

    protected function request_stub($name)
    {
        //gives model with replaced placeholder
        $template = str_replace(
            ['{{modelName}}'],
            [$name], //name comes from command
            $this->getStub('request')
        );

        //create file dir if it doesnot exist
        if (!file_exists($path = app_path("/Http/Requests"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Http/Requests/{$name}Request.php"), $template);
    }

    protected function controller_stub($name)
    {
        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralKebabCase}}'
            ],

            [
                $name,
                $this->snake_case,
                $this->snake_case_plural,
                $this->kebab_case_plural
            ],

            $this->getStub('controller')
        );

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Http/Controllers/{$name}Controller.php"), $template);
    }

    protected function factory_stub($name)
    {
        //gives model with replaced placeholder
        $template = str_replace(
            ['{{modelName}}'],
            [$name], //name comes from command
            $this->getStub('factory')
        );

        //create file dir if it doesnot exist
        if (!file_exists($path = base_path("/database/factories"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(base_path("/database/factories/{$name}Factory.php"), $template);
    }

    protected function blade_stub($name)
    {
        $template1 = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralKebabCase}}',
            ],

            [
                $name,
                $this->snake_case,
                $this->snake_case_plural,
                $this->kebab_case_plural
            ],
            $this->getBladeStub('index_blade')
        );

        $template2 = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralKebabCase}}'
            ],

            [
                $name,
                $this->kebab_case_plural
            ],
            $this->getBladeStub('create_blade')
        );

        $template3 = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralKebabCase}}',
            ],

            [
                $name,
                $this->snake_case,
                $this->kebab_case_plural

            ],
            $this->getBladeStub('edit_blade')
        );

        $template4 = $this->getBladeStub('show_blade');

        //create folder if it doesnot exist
        if (!file_exists($path = base_path("/resources/views/" . $this->snake_case))) {
            mkdir($path, 0777, true);
        }

        //create file
        file_put_contents(base_path("/resources/views/" . $this->snake_case . "/index.blade.php"), $template1);
        file_put_contents(base_path("/resources/views/" . $this->snake_case . "/create.blade.php"), $template2);
        file_put_contents(base_path("/resources/views/" . $this->snake_case . "/edit.blade.php"), $template3);
        file_put_contents(base_path("/resources/views/" . $this->snake_case . "/show.blade.php"), $template4);
    }

    protected function feature_test_stub($name)
    {
        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}'
            ],

            [
                $name,
                $this->snake_case,
                $this->snake_case_plural
            ],
            $this->getStub('feature_test')
        );

        //update placeholder_model with valued Model
        file_put_contents(base_path("/tests/Feature/{$name}Test.php"), $template);
    }

    //FOR FOLDER SPECIFIC

    protected function named_controller_stub($name, $folder_name)
    {
        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{folderName}}',
                '{{folderNameSnakeCase}}',
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralKebabCase}}'
            ],

            [
                $folder_name,
                Str::snake($folder_name),
                $name,
                $this->snake_case,
                $this->snake_case_plural,
                $this->kebab_case_plural
            ],

            $this->getStub('named_controller')
        );

        //create folder if it doesnot exist
        if (!file_exists($path = app_path("/Http/Controllers/{$folder_name}"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Http/Controllers/{$folder_name}/{$name}Controller.php"), $template);
    }

    protected function named_blade_stub($name, $folder_name)
    {
        $template1 = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralKebabCase}}',
            ],

            [
                $name,
                $this->snake_case,
                $this->snake_case_plural,
                $this->kebab_case_plural
            ],
            $this->getBladeStub('index_blade')
        );

        $template2 = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralKebabCase}}'
            ],

            [
                $name,
                $this->kebab_case_plural
            ],
            $this->getBladeStub('create_blade')
        );

        $template3 = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralKebabCase}}'
            ],

            [
                $name,
                $this->snake_case,
                $this->kebab_case_plural
            ],
            $this->getBladeStub('edit_blade')
        );

        $template4 = $this->getBladeStub('show_blade');

        //create folder if it doesnot exist
        if (!file_exists($path = base_path("/resources/views/" . Str::snake($folder_name) . "/" . $this->snake_case))) {
            mkdir($path, 0777, true);
        }

        //create file
        file_put_contents(base_path("/resources/views/" . Str::snake($folder_name) . "/" . $this->snake_case . "/index.blade.php"), $template1);
        file_put_contents(base_path("/resources/views/" . Str::snake($folder_name) . "/" . $this->snake_case . "/create.blade.php"), $template2);
        file_put_contents(base_path("/resources/views/" . Str::snake($folder_name) . "/" . $this->snake_case . "/edit.blade.php"), $template3);
        file_put_contents(base_path("/resources/views/" . Str::snake($folder_name) . "/" . $this->snake_case . "/show.blade.php"), $template4);
    }
}
