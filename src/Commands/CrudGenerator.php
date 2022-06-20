<?php

namespace Niraj\CrudStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Niraj\CrudStarter\Traits\CommonCode;
use Niraj\CrudStarter\Traits\logoTrait;
use Niraj\CrudStarter\Traits\tableTrait;

class CrudGenerator extends Command
{
    use tableTrait, logoTrait, CommonCode;

    protected $signature = "gen:crud {name} {--fields=} {{--addFileTrait}}";

    protected $description = 'Generates Basic Laravel Crud :)';

    public function __construct()
    {
        parent::__construct();

        //check if stub files are published
        if (file_exists(resource_path('crud-stub'))) {
            $this->stub_path = resource_path('crud-stub');
        } else {
            $this->stub_path = base_path('vendor/niraj/crudstarter/src/stubs');
        }
    }

    public function handle()
    {
        $name = $this->argument('name');
        $fields = $this->option('fields');
        $addFileTrait = $this->option('addFileTrait');

        //traits
        $this->tableArray = array();
        $this->show_logo();

        //define variable
        $this->plural = Str::plural($name);
        $this->snake_case = Str::snake($name);
        $this->snake_case_plural = Str::plural(Str::snake($name));
        $this->kebab_case_plural = Str::plural(Str::kebab($name));

        $this->migration_stub($name, $fields);

        $this->publish_components();

        if ($this->confirm('Do you want to add controllers in specific folder ?')) {

            $folder_name = $this->ask('Enter the Folder Name');

            //add named resource controller in web.php
            File::append(
                base_path('routes/web.php'),
                'Route::resource(\'' . $this->kebab_case_plural . "',\\App\Http\Controllers\\" . $folder_name . "\\" . $name . "Controller::class);" . PHP_EOL
            );

            $this->named_controller_stub($name, $folder_name);
            $this->named_request_stub($name, $folder_name, $fields);
            $this->named_model_stub($name, $folder_name, $fields);
            $this->named_blade_stub($name, $folder_name, $fields);

            //to generate test
            if ($fields != '') {
                if ($this->confirm('Do you wish to generate Test?')) {
                    $this->named_feature_test_stub($name, $folder_name, $fields);
                    $this->tableArray[] = ['Feature Test', '<info>created</info>'];
                }
            }
        } else {
            //add resource controller in web.php
            File::append(
                base_path('routes/web.php'),
                'Route::resource(\'' . $this->kebab_case_plural . "',\\App\Http\Controllers\\" . $name . "Controller::class);" . PHP_EOL
            );

            $this->controller_stub($name);
            $this->request_stub($name, $fields);
            $this->model_stub($name, $fields);
            $this->blade_stub($name, $fields);

            //to generate test
            if ($fields != '') {
                if ($this->confirm('Do you wish to generate Test?')) {
                    $this->feature_test_stub($name, $fields);
                    $this->tableArray[] = ['Feature Test', '<info>created</info>'];
                }
            }
        }

        $this->tableArray = [['Model', '<info>created</info>'], ['Controller', '<info>created</info>'], ['Migration', '<info>created</info>'], ['Form Request', '<info>created</info>'], ['Blade Files', '<info>created</info>']];

        //to add file trait helper
        if (is_bool($addFileTrait) && $addFileTrait === true) {
            $this->add_file_trait();
        }

        $this->showTableInfo($this->tableArray, 'Crud generated');
    }

    protected function getStub($type)
    {
        return file_get_contents("$this->stub_path/$type.stub");
    }

    protected function getBladeStub($type)
    {
        return file_get_contents("$this->stub_path/blade/$type.stub");
    }

    protected function migration_stub($name, $fields = '')
    {
        $migrationSchema = $this->resolve_migration($fields);

        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelNamePlural}}',
                '{{modelNamePluralLowerCase}}',
                '{{migrationSchema}}'
            ],

            [
                $this->plural,
                $this->snake_case_plural,
                $migrationSchema
            ],

            $this->getStub('migration')
        );

        $path = database_path('/migrations/') . date('Y_m_d_His') . '_create_' . $this->snake_case_plural . '_table.php';

        file_put_contents($path, $template);
    }

    protected function model_stub($name, $fields = '')
    {
        $massAssignment = "protected \$guarded = [];";

        if ($fields != '') {

            $fieldsArray = explode(' ', $fields);

            foreach ($fieldsArray as $item) {
                $single_value = explode(':', trim($item));
                $fillableArray[] = $single_value[0];
            }

            $commaSeparetedString = implode("', '", $fillableArray);

            $massAssignment = "protected \$fillable = ['" . $commaSeparetedString . "'];";
        }
        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{massAssignment}}'
            ],
            [
                $name,
                $massAssignment
            ],
            $this->getStub('model')
        );

        //create file dir if it doesnot exist
        if (!file_exists($path = app_path("/Models"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Models/{$name}.php"), $template);
    }

    protected function request_stub($name, $fields)
    {
        $validationRules = $this->resolve_request($fields);
        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{validationRules}}'
            ],
            [
                $name,
                $validationRules
            ],
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

    protected function blade_stub($name, $fields)
    {
        $fieldsForCreate = $this->get_fields_for_create($fields);

        $fieldsForEdit = $this->get_fields_for_edit($fields, $this->snake_case);

        //uncomment when in create and edit use same blade file
        //$fieldsForCreateEdit = $this->get_fields_create_and_edit($fields, $this->snake_case);

        $rows_for_index = $this->get_rows_for_index($fields, $this->snake_case);
        $thead_for_index = $this->get_thead_for_index($fields);

        $template1 = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralKebabCase}}',
                '{{rowsForIndex}}',
                '{{theadForIndex}}',
            ],

            [
                $name,
                $this->snake_case,
                $this->snake_case_plural,
                $this->kebab_case_plural,
                $rows_for_index,
                $thead_for_index,
            ],
            $this->getBladeStub('index_blade')
        );

        $template2 = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralKebabCase}}',
                '{{fieldsForCreate}}'
            ],

            [
                $name,
                $this->kebab_case_plural,
                $fieldsForCreate
                //$fieldsForCreateEdit // just uncomment to use it
            ],
            $this->getBladeStub('create_blade')
        );

        $template3 = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralKebabCase}}',
                '{{fieldsForEdit}}'
            ],

            [
                $name,
                $this->snake_case,
                $this->kebab_case_plural,
                $fieldsForEdit
            ],
            $this->getBladeStub('edit_blade')
        );

        $template4 = $this->getBladeStub('show_blade');

        //create folder if it doesnot exist
        if (!file_exists($path = base_path("/resources/views/" . $this->snake_case))) {
            mkdir($path, 0777, true);
        }

        //creates main.blade.php
        $this->create_main_layout();

        //create file
        file_put_contents(base_path("/resources/views/" . $this->snake_case . "/index.blade.php"), $template1);
        file_put_contents(base_path("/resources/views/" . $this->snake_case . "/create.blade.php"), $template2);
        file_put_contents(base_path("/resources/views/" . $this->snake_case . "/edit.blade.php"), $template3);
        file_put_contents(base_path("/resources/views/" . $this->snake_case . "/show.blade.php"), $template4);
    }

    protected function publish_components()
    {
        if (!file_exists($path = resource_path('/views/components'))) {
            mkdir($path, 0777, true);
        }

        \File::copyDirectory(__DIR__ . '/../components', resource_path("/views/components"));
    }

    protected function create_main_layout()
    {
        //create file dir if it doesnot exist && create main.blade file if it doesnot exist
        if (!file_exists($path = resource_path("/views/layouts"))) {
            mkdir($path, 0777, true);
        }

        if (!file_exists($path = resource_path("/views/layouts/main.blade.php"))) {
            file_put_contents(base_path("/resources/views/layouts/main.blade.php"), $this->getBladeStub('main_blade'));
        }
    }

    protected function feature_test_stub($name, $fields = '')
    {
        $createTestFields = $this->resolve_create_test_fields($fields);

        $updateTestFields = $this->resolve_update_test_fields($fields);

        $firstFieldForUpdate = $this->resolve_first_of_update_field($fields);

        //gives test stub with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{createTestFields}}',
                '{{updateTestFields}}',
                '{{firstFieldForUpdate}}'
            ],

            [
                $name,
                $this->snake_case,
                $this->kebab_case_plural,
                $createTestFields,
                $updateTestFields,
                $firstFieldForUpdate
            ],
            $this->getStub('feature_test')
        );

        //update placeholder_model with valued Model
        file_put_contents(base_path("/tests/Feature/{$name}Test.php"), $template);
    }

    //FOR FOLDER SPECIFIC------------------------------------------------------------

    protected function named_controller_stub($name, $folder_name)
    {
        //gives named controller stub with replaced placeholder
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

    protected function named_blade_stub($name, $folder_name, $fields)
    {
        $fieldsForCreate = $this->get_fields_for_create($fields);

        $fieldsForEdit = $this->get_fields_for_edit($fields, $this->snake_case);

        $rows_for_index = $this->get_rows_for_index($fields, $this->snake_case);
        $thead_for_index = $this->get_thead_for_index($fields);

        //$fieldsForCreateEdit = $this->get_fields_create_and_edit($fields, $this->snake_case);

        $template1 = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralKebabCase}}',
                '{{rowsForIndex}}',
                '{{theadForIndex}}',
            ],

            [
                $name,
                $this->snake_case,
                $this->snake_case_plural,
                $this->kebab_case_plural,
                $rows_for_index,
                $thead_for_index,
            ],
            $this->getBladeStub('index_blade')
        );

        $template2 = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralKebabCase}}',
                '{{fieldsForCreate}}'
            ],

            [
                $name,
                $this->kebab_case_plural,
                $fieldsForCreate,
                //$fieldsForCreateEdit // just uncomment to use it
            ],
            $this->getBladeStub('create_blade')
        );

        $template3 = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralKebabCase}}',
                '{{fieldsForEdit}}'
            ],

            [
                $name,
                $this->snake_case,
                $this->kebab_case_plural,
                $fieldsForEdit
            ],
            $this->getBladeStub('edit_blade')
        );

        $template4 = $this->getBladeStub('show_blade');

        //creates main.blade.php
        $this->create_main_layout();

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

    protected function named_request_stub($name, $folder_name, $fields)
    {
        $validationRules = $this->resolve_request($fields);
        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{folderName}}',
                '{{validationRules}}'
            ],
            [
                $name,
                $folder_name,
                $validationRules
            ],
            $this->getStub('named_request')
        );

        //create file dir if it doesnot exist
        if (!file_exists($path = app_path("/Http/Requests/{$folder_name}"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Http/Requests/{$folder_name}/{$name}Request.php"), $template);
    }

    protected function named_model_stub($name, $folder_name, $fields = '')
    {
        $massAssignment = "protected \$guarded = [];";

        if ($fields != '') {

            $fieldsArray = explode(' ', $fields);

            foreach ($fieldsArray as $item) {
                $single_value = explode(':', trim($item));
                $fillableArray[] = $single_value[0];
            }

            $commaSeparetedString = implode("', '", $fillableArray);

            $massAssignment = "protected \$fillable = ['" . $commaSeparetedString . "'];";
        }
        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{folderName}}',
                '{{massAssignment}}'
            ],
            [
                $name,
                $folder_name,
                $massAssignment
            ],
            $this->getStub('named_model')
        );

        //create file dir if it doesnot exist
        if (!file_exists($path = app_path("/Models/{$folder_name}"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Models/{$folder_name}/{$name}.php"), $template);
    }

    protected function named_feature_test_stub($name, $folder_name, $fields = '')
    {
        $createTestFields = $this->resolve_create_test_fields($fields);

        $updateTestFields = $this->resolve_update_test_fields($fields);

        $firstFieldForUpdate = $this->resolve_first_of_update_field($fields);

        //gives test stub with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{folderName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{createTestFields}}',
                '{{updateTestFields}}',
                '{{firstFieldForUpdate}}'
            ],

            [
                $name,
                $folder_name,
                $this->snake_case,
                $this->kebab_case_plural,
                $createTestFields,
                $updateTestFields,
                $firstFieldForUpdate
            ],
            $this->getStub('named_feature_test')
        );

        //update placeholder_model with valued Model
        file_put_contents(base_path("/tests/Feature/{$name}Test.php"), $template);
    }


    //END OF FOLDER SPECIFIC------------------------------------------------------------

    protected function add_file_trait()
    {
        if (!file_exists($path = app_path("/Traits"))) {
            mkdir($path, 0777, true);
        }

        copy(__DIR__ . '/../stubs/Traits/FileUploadTrait.php', app_path('/Traits/FileUploadTrait.php'));
    }
}
