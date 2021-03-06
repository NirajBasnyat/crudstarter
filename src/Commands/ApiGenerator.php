<?php

namespace Niraj\CrudStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Niraj\CrudStarter\Traits\CommonCode;
use Niraj\CrudStarter\Traits\logoTrait;
use Niraj\CrudStarter\Traits\tableTrait;

class ApiGenerator extends Command
{
    use tableTrait, logoTrait, CommonCode;

    protected $signature = 'gen:api {name} {--fields=}';

    protected $description = 'Generates Basic Laravel Api :D';

    public function __construct()
    {
        parent::__construct();

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

        //traits
        $this->tableArray = array();
        $this->show_logo();

        //define variable
        $this->plural = Str::plural($name);
        $this->snake_case = Str::snake($name);
        $this->snake_case_plural = Str::plural(Str::snake($name));
        $this->kebab_case_plural = Str::plural(Str::kebab($name));

        if ($this->confirm('Do you want to add controllers in specific folder ?')) {

            $folder_name = $this->ask('Enter the Folder Name');

            //add api resource controller in api.php
            File::append(
                base_path('routes/api.php'),
                'Route::apiResource(\'' . $this->kebab_case_plural . "',\\App\Http\Controllers\Api\\" . $folder_name . "\\" . $name . "ApiController::class);" . PHP_EOL
            );

            $this->named_api_controller_stub($name, $folder_name);
            $this->named_resource_stub($name, $folder_name, $fields);
            $this->named_api_request_stub($name, $folder_name, $fields);

            if ($this->confirm('Do you wish to generate Model and Migration ?')) {
                $this->named_model_stub($name, $folder_name, $fields);
                $this->migration_stub($name, $fields);

                $this->tableArray [] = ['Model', '<info>created</info>'];
                $this->tableArray [] = ['Migration', '<info>created</info>'];
            }

            //to generate test
            if ($fields != '') {
                if ($this->confirm('Do you wish to generate Test?')) {
                    $this->named_api_feature_test_stub($name, $folder_name, $fields);
                    $this->tableArray[] = ['Feature Test', '<info>created</info>'];
                }
            }

        } else {

            File::append(base_path('routes/api.php'),
                'Route::apiResource(\'' . $this->kebab_case_plural . "',\\App\Http\Controllers\Api\\" . $name . "ApiController::class);" . PHP_EOL);

            //call generation methods
            $this->api_controller_stub($name);
            $this->resource_stub($name, $fields);
            $this->api_request_stub($name, $fields);

            if ($this->confirm('Do you wish to generate Model and Migration ?')) {
                $this->model_stub($name, $fields);
                $this->migration_stub($name, $fields);

                $this->tableArray [] = ['Model', '<info>created</info>'];
                $this->tableArray [] = ['Migration', '<info>created</info>'];
            }

            if ($fields != '') {
                if ($this->confirm('Do you wish to generate Tests for Api?')) {
                    $this->api_feature_test_stub($name, $fields);
                    $this->tableArray [] = ['Feature Test', '<info>created</info>'];
                }
            }
        }

        $this->tableArray = [['Api Controller', '<info>created</info>'], ['Resource', '<info>created</info>'], ['Form Request', '<info>created</info>']];

        $this->showTableInfo($this->tableArray, 'API generated');
    }

    protected function getStub($type)
    {
        return file_get_contents("$this->stub_path/$type.stub");
    }

    protected function getApiStub($type)
    {
        return file_get_contents("$this->stub_path/api_stubs/$type.stub");
    }

    protected function api_controller_stub($name)
    {
        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
            ],

            [
                $name,
                $this->snake_case
            ],

            $this->getApiStub('api_controller')
        );

        //create folder if it doesnot exist
        if (!file_exists($path = app_path("/Http/Controllers/Api"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Http/Controllers/Api/{$name}ApiController.php"), $template);
    }

    protected function resource_stub($name, $fields = '')
    {
        $resourceContent = '';

        if ($fields != '') {

            $fieldsArray = explode(' ', $fields);

            $data = array();

            $iteration = 0;
            foreach ($fieldsArray as $field) {
                $fieldArraySingle = explode(':', $field);
                $data[$iteration]['name'] = trim($fieldArraySingle[0]);

                $iteration++;
            }

            foreach ($data as $item) {
                $resourceContent .= "'" . $item['name'] . "' " . '=> ' . "\$this->" . $item['name'] . ',' . PHP_EOL . '            ';
            }
        }

        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{resourceContent}}'
            ],
            [
                $name,
                $resourceContent
            ],

            $this->getApiStub('resource')
        );

        //create folder if it doesnot exist
        if (!file_exists($path = app_path("/Http/Resources"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Http/Resources/{$name}Resource.php"), $template);
    }

    protected function api_request_stub($name, $fields)
    {
        $validationRules = $this->resolve_request($fields);

        $template = str_replace(
            [
                '{{modelName}}',
                '{{validationRules}}'
            ],
            [
                $name,
                $validationRules
            ],
            $this->getApiStub('api_request')
        );

        //create file dir if it doesnot exist
        if (!file_exists($path = app_path("/Http/Requests"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Http/Requests/{$name}ApiRequest.php"), $template);

        $api_form_request = $this->getApiStub('ApiFormRequest');

        if (!file_exists(app_path("/Http/Requests/ApiFormRequest.php"))) {
            file_put_contents(app_path("/Http/Requests/ApiFormRequest.php"), $api_form_request);
        }
    }

    //FOR TEST
    protected function api_feature_test_stub($name, $fields)
    {
        $createTestFields = $this->resolve_create_test_fields($fields);

        $updateTestFields = $this->resolve_update_test_fields($fields);

        $firstFieldForUpdate = $this->resolve_first_of_update_field($fields);

        //gives model with replaced placeholder
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
            $this->getApiStub('api_feature_test')
        );

        //update placeholder_model with valued Model
        file_put_contents(base_path("/tests/Feature/{$name}ApiTest.php"), $template);
    }

    //  FOR ADDITIONAL GENERATION

    protected function model_stub($name, $fields = '')
    {
        $traitImport = '';
        $traits = '';

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
                '{{massAssignment}}',
                '{{traitImport}}',
                '{{traits}}'
            ],
            [
                $name,
                $massAssignment,
                $traitImport,
                $traits
            ],
            $this->getStub('model')
        );

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Models/{$name}.php"), $template);
    }

    protected function migration_stub($name, $fields)
    {
        $softDelete = '';

        $migrationSchema = $this->resolve_migration($fields);

        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelNamePlural}}',
                '{{modelNamePluralLowerCase}}',
                '{{migrationSchema}}',
                '{{softDelete}}'
            ],

            [
                $this->plural,
                $this->snake_case_plural,
                $migrationSchema,
                $softDelete
            ],

            $this->getStub('migration')
        );

        $path = database_path('/migrations/') . date('Y_m_d_His') . '_create_' . $this->snake_case_plural . '_table.php';

        file_put_contents($path, $template);
    }

    //FOR NAMED/ FOLDERED GENERATION

    protected function named_api_controller_stub($name, $folder_name)
    {
        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{folderName}}'
            ],

            [
                $name,
                $this->snake_case,
                $folder_name
            ],

            $this->getApiStub('named_api_controller')
        );

        //create folder if it doesnot exist
        if (!file_exists($path = app_path("/Http/Controllers/Api/{$folder_name}"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Http/Controllers/Api/{$folder_name}/{$name}ApiController.php"), $template);
    }

    protected function named_resource_stub($name, $folder_name, $fields = '')
    {
        $resourceContent = '';

        if ($fields != '') {

            $fieldsArray = explode(' ', $fields);

            $data = array();

            $iteration = 0;
            foreach ($fieldsArray as $field) {
                $fieldArraySingle = explode(':', $field);
                $data[$iteration]['name'] = trim($fieldArraySingle[0]);

                $iteration++;
            }

            foreach ($data as $item) {
                $resourceContent .= "'" . $item['name'] . "' " . '=> ' . "\$this->" . $item['name'] . ',' . PHP_EOL . '            ';
            }
        }

        //gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{folderName}}',
                '{{resourceContent}}'
            ],
            [
                $name,
                $folder_name,
                $resourceContent
            ],

            $this->getApiStub('named_resource')
        );

        //create folder if it doesnot exist
        if (!file_exists($path = app_path("/Http/Resources/{$folder_name}"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Http/Resources/{$folder_name}/{$name}Resource.php"), $template);
    }

    protected function named_api_request_stub($name, $folder_name, $fields)
    {
        $validationRules = $this->resolve_request($fields);

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
            $this->getApiStub('named_api_request')
        );

        //create file dir if it doesnot exist
        if (!file_exists($path = app_path("/Http/Requests/{$folder_name}Api"))) {
            mkdir($path, 0777, true);
        }

        //update placeholder_model with valued Model
        file_put_contents(app_path("/Http/Requests/{$folder_name}Api/{$name}ApiRequest.php"), $template);

        $api_form_request = $this->getApiStub('ApiFormRequest');

        if (!file_exists(app_path("/Http/Requests/ApiFormRequest.php"))) {
            file_put_contents(app_path("/Http/Requests/ApiFormRequest.php"), $api_form_request);
        }
    }

    protected function named_model_stub($name, $folder_name, $fields = '')
    {
        $traitImport = '';
        $traits = '';

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
                '{{massAssignment}}',
                '{{traitImport}}',
                '{{traits}}'
            ],
            [
                $name,
                $folder_name,
                $massAssignment,
                $traitImport,
                $traits
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

    //FOR TEST
    protected function named_api_feature_test_stub($name, $folder_name, $fields)
    {
        $createTestFields = $this->resolve_create_test_fields($fields);

        $updateTestFields = $this->resolve_update_test_fields($fields);

        $firstFieldForUpdate = $this->resolve_first_of_update_field($fields);

        //gives model with replaced placeholder
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
            $this->getApiStub('named_api_feature_test')
        );

        //update placeholder_model with valued Model
        file_put_contents(base_path("/tests/Feature/{$name}ApiTest.php"), $template);
    }

}
