<?php

namespace Niraj\CrudStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Niraj\CrudStarter\Traits\logoTrait;
use Niraj\CrudStarter\Traits\ResolveCodeTrait;
use Niraj\CrudStarter\Traits\tableTrait;

class ApiGenerator extends Command
{
    use tableTrait, logoTrait, ResolveCodeTrait;

    protected $signature = 'gen:api {name} {--fields=} {--relations=} {--softDelete}';

    protected $description = 'Generates Laravel Api';

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
        $relations = $this->option('relations');
        $softDelete = $this->option('softDelete');

        $this->initializeVariables($name);

        if ($this->confirm('Do you want to add controllers in a specific folder?')) {
            $folder_name = $this->ask('Enter the Folder Name');
            $this->addRoutesAndFiles($folder_name, $name, $fields, $relations);
        } else {
            $this->addRoutesAndFiles(null, $name, $fields, $relations);
        }

        $this->showTableInfo($this->tableArray, 'API Generated');
    }

    protected function initializeVariables($name)
    {
        $this->tableArray = [];
        $this->show_logo();
        $this->plural = Str::plural($name);
        $this->snake_case = Str::snake($name);
        $this->snake_case_plural = Str::plural($this->snake_case);
        $this->kebab_case_plural = Str::plural(Str::kebab($name));
    }

    protected function addRoutesAndFiles($folder_name, $name, $fields, $relations)
    {
        $hasSoftDeletes = $this->hasSoftDeletes();

        $data = '';
        $data .= 'Route::post(\''.$this->kebab_case_plural.'/{id}/restore'.'\',[\\App\Http\Controllers\Api\\'.($folder_name ? $folder_name."\\" : '').$name.'ApiController::class,'.'\'restore'.'\'])->name(\''.$this->kebab_case_plural.'.restore\');'.PHP_EOL;
        $data .= 'Route::post(\''.$this->kebab_case_plural.'/{id}/force-delete'.'\',[\\App\Http\Controllers\Api\\'.($folder_name ? $folder_name."\\" : '').$name.'ApiController::class,'.'\'forceDelete'.'\'])->name(\''.$this->kebab_case_plural.'.force_delete\');'.PHP_EOL;
        $data .= 'Route::apiResource(\''.$this->kebab_case_plural."',\\App\Http\Controllers\Api\\".($folder_name ? $folder_name."\\" : '').$name."ApiController::class);".PHP_EOL;

        if ($hasSoftDeletes) {
            File::append(base_path('routes/api.php'), $data);
        } else {
            File::append(base_path('routes/api.php'), 'Route::apiResource(\''.$this->kebab_case_plural."',\\App\Http\Controllers\Api\\".($folder_name ? $folder_name."\\" : '').$name."ApiController::class);".PHP_EOL);
        }

        if ($folder_name) {
            $this->generate_api_controller_stub($name, $folder_name, $fields);
            $this->generate_resource_stub($name, $folder_name, $fields);
            $this->generate_api_request_stub($name, $folder_name, $fields);

            if ($this->confirm('Do you wish to generate Model and Migration ?')) {
                $this->generate_model_stub($name, $folder_name, $fields, $relations);
                $this->generate_migration_stub($name, $fields);

                $this->tableArray [] = ['Model', '<info>Created</info>'];
                $this->tableArray [] = ['Migration', '<info>Created</info>'];

                //TODO generate test
                /*  if ($fields != '') {
                      if ($this->confirm('Do you wish to generate Test?')) {
                          $this->named_api_feature_test_stub($name, $folder_name, $fields);
                          $this->tableArray[] = ['Feature Test', '<info>Created</info>'];
                      }
                  }*/
            }
        } else {
            $this->generate_api_controller_stub($name, null, $fields);
            $this->generate_resource_stub($name, null, $fields);
            $this->generate_api_request_stub($name, null, $fields);

            if ($this->confirm('Do you wish to generate Model and Migration ?')) {
                $this->generate_model_stub($name, null, $fields, $relations);
                $this->generate_migration_stub($name, $fields);

                $this->tableArray [] = ['Model', '<info>Created</info>'];
                $this->tableArray [] = ['Migration', '<info>Created</info>'];
            }

            /*if ($fields != '') {
                if ($this->confirm('Do you wish to generate Tests for Api?')) {
                    $this->api_feature_test_stub($name, $fields);
                    $this->tableArray [] = ['Feature Test', '<info>Created</info>'];
                }
            }*/
        }
        $this->tableArray = array_merge($this->tableArray, [['Api Controller', '<info>Created</info>'], ['Api Resource', '<info>Created</info>'], ['Form Request', '<info>Created</info>']]);
    }

    protected function getStub($type)
    {
        return file_get_contents("$this->stub_path/crud_stubs/$type.stub");
    }

    protected function getApiStub($type)
    {
        return file_get_contents("$this->stub_path/api_stubs/$type.stub");
    }

    protected function generate_resource_stub(string $name, ?string $folder_name, string $fields = '')
    {
        $resourceContent = '';

        if ($fields != '') {
            $fieldsArray = explode(' ', $fields);
            $data = [];

            foreach ($fieldsArray as $field) {
                $fieldArraySingle = explode(':', $field);
                $data[] = ['name' => trim($fieldArraySingle[0])];
            }

            foreach ($data as $item) {
                $resourceContent .= "'{$item['name']}' => \$this->{$item['name']},\n            ";
            }
        }

        $template = str_replace(
            [
                '{{modelName}}',
                '{{folderName}}',
                '{{resourceContent}}'
            ],
            [
                $name,
                $folder_name ?? '',
                $resourceContent
            ],
            $this->getApiStub($folder_name ? 'named_resource' :'resource')
        );

        //create folder if it does not exist
        $path = app_path("/Http/Resources".($folder_name ? "/".$folder_name : ""));

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents("{$path}/{$name}Resource.php", $template);
    }

    protected function generate_api_request_stub($name, $folder_name, $fields)
    {
        $validationRules = $this->resolve_request($fields);
        // Gives model with replaced placeholder
        $storeTemplate = str_replace(
            [
                '{{modelName}}',
                '{{folderName}}',
                '{{validationRules}}'
            ],
            [
                $name,
                $folder_name ?? '',
                $validationRules
            ],
            $this->getApiStub($folder_name ? 'named_api_request' : 'api_request')
        );

        $updateTemplate = str_replace(
            [
                '{{modelName}}',
                '{{folderName}}',
                '{{validationRules}}'
            ],
            [
                $name,
                $folder_name ?? '',
                $validationRules
            ],
            $this->getApiStub($folder_name ? 'named_api_update_request' : 'api_update_request')
        );

        // Create file dir if it does not exist
        $path = app_path("/Http/Requests/").($folder_name ? "/{$folder_name}Api" : 'Api');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Update placeholder_model with valued Model
        file_put_contents("{$path}/{$name}StoreApiRequest.php", $storeTemplate);
        file_put_contents("{$path}/{$name}UpdateApiRequest.php", $updateTemplate);
    }


    //  FOR ADDITIONAL GENERATION

    private function generate_model_stub($name, $folder_name, $fields, $relations)
    {
        $traitImport = '';
        $traits = '';

        if ($this->hasSoftDeletes() == true) {
            $traitImport = 'use Illuminate\Database\Eloquent\SoftDeletes;';
            $traits = 'use SoftDeletes;';
        }

        $modelNameSingularLowerCase = Str::kebab($name);

        $fileTraitCodes = $this->resolve_model_should_have_file_trait($fields, $modelNameSingularLowerCase);

        $massAssignment = "protected \$guarded = [];";

        $processed_relations = '';

        if (!is_null($relations)) {
            $processed_relations = $this->setRelationships($relations, $folder_name);
        }

        // Gives model with replaced placeholder
        $template = str_replace(
            [
                '{{modelName}}',
                '{{folderName}}',
                '{{massAssignment}}',
                '{{traitImport}}',
                '{{traits}}',
                '{{relationships}}',
                '{{fileTraitImport}}',
                '{{fileTrait}}',
                '{{fileAccessor}}',
            ],
            [
                $name,
                $folder_name ?? '',
                $massAssignment,
                $traitImport,
                $traits,
                $processed_relations,
                $fileTraitCodes['traitImport'],
                $fileTraitCodes['trait'],
                $fileTraitCodes['getImagePathAttribute'],
            ],
            $this->getStub($folder_name ? 'named_model' : 'model')
        );

        // Create directory if it does not exist
        $path = app_path("/Models");

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Update placeholder_model with the valued Model
        file_put_contents("{$path}/{$name}.php", $template);
    }


    protected function generate_migration_stub(string $name, string $fields = '')
    {
        $softDelete = '';

        if ($this->hasSoftDeletes() == true) {
            $softDelete = '$table->softDeletes();';
        }

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

        $path = database_path('/migrations/').date('Y_m_d_His').'_create_'.$this->snake_case_plural.'_table.php';

        file_put_contents($path, $template);
    }

    //FOR NAMED/ FOLDERED GENERATION

    private function generate_api_controller_stub(string $name, $folder_name, string $fields)
    {
        $methodCodes = $this->generate_controller_method_codes($name, $fields);

        $controller_stub = $this->getApiStub('api_controller');

        if ($this->hasSoftDeletes() == true) {
            $controller_stub = $this->getApiStub('soft_delete_api_controller');
        }

        if ($folder_name) {
            $controller_stub = $this->getApiStub('named_api_controller');

             if ($this->hasSoftDeletes() == true) {
                 $controller_stub = $this->getApiStub('soft_delete_named_api_controller');
             }
        }

        $controllerTemplate = str_replace(
            [
                '{{folderName}}',
                '{{folderNameSnakeCase}}',
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralKebabCase}}',
                '{{storeMethodCode}}',
                '{{updateMethodCode}}',
                '{{deleteMethodCode}}'
            ],
            [
                $folder_name ?? '',
                $folder_name ? Str::snake($folder_name) : '',
                $name,
                $this->snake_case,
                $this->snake_case_plural,
                $this->kebab_case_plural,
                $methodCodes['store'],
                $methodCodes['update'],
                $methodCodes['delete'],
            ],
            $controller_stub
        );

        $path = app_path("/Http/Controllers/").($folder_name ? "/{$folder_name}Api" : 'Api');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Update placeholder_model with valued Model
        file_put_contents("{$path}/{$name}ApiController.php", $controllerTemplate);
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

    protected function hasSoftDeletes(): bool
    {
        return is_bool($this->option('softDelete')) && $this->option('softDelete') === true;
    }
}
