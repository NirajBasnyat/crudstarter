<?php

namespace Niraj\CrudStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Niraj\CrudStarter\Traits\logoTrait;
use Niraj\CrudStarter\Traits\resolveCodeTrait;
use Niraj\CrudStarter\Traits\tableTrait;

class CrudGenerator extends Command
{
    use tableTrait, logoTrait, resolveCodeTrait;

    protected $signature = "gen:crud {name} {--fields=} {--relations=} {--softDelete}";

    protected $description = 'Generates Laravel Crud :)';

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
        $this->show_logo();

        //get the args
        $name = $this->argument('name');
        $fields = $this->option('fields');
        $relations = $this->option('relations');
        $softDelete = $this->option('softDelete');

        $this->initializeVariables($name);

        //add routes
        if ($this->confirm('Do you want to add controllers in a specific folder?')) {
            $folder_name = $this->ask('Enter the Folder Name');
            $this->addRoutesAndFiles($folder_name, $name, $fields, $relations);
        } else {
            $this->addRoutesAndFiles(null, $name, $fields, $relations);
        }

        $this->generateMigrationStub($name, $fields);
        $this->publishComponents();

        //generate table
        $this->tableArray = [['Model', '<info>Created</info>'], ['Controller', '<info>Created</info>'], ['Migration', '<info>Created</info>'], ['Form Request', '<info>Created</info>'], ['Blade Files', '<info>Created</info>']];

        $this->showTableInfo($this->tableArray, 'Crud Generated');
    }

    protected function initializeVariables($name)
    {
        $this->tableArray = [];
        $this->plural = Str::plural($name);
        $this->snake_case = Str::snake($name);
        $this->snake_case_plural = Str::plural($this->snake_case);
        $this->kebab_case_plural = Str::plural(Str::kebab($name));
        $this->kebab_case_singular = Str::kebab($name);
    }

    protected function addRoutesAndFiles($folder_name, $name, $fields, $relations)
    {
        $routePath = base_path('routes/' . config('crudstarter.crud_route', 'web.php'));

        $formatted_folder_name = $folder_name ? Str::snake($folder_name) : null;

        $hasSoftDeletes = $this->hasSoftDeletes();

        $hasStatusField = $this->field_has_status($fields);

        $softDeleteRoutes = !is_null($folder_name) ? "Route::group(['middleware' => 'auth', 'prefix' => '$formatted_folder_name', 'as' => '$formatted_folder_name.'], function () {".PHP_EOL : '';
        $softDeleteRoutes .= $hasStatusField ? 'Route::get(\'status-change-'.$this->snake_case."',[\\App\Http\Controllers\\".($folder_name ? $folder_name."\\" : '').$name."Controller::class,'changeStatus'])->name("."'status-change-".$this->snake_case."');".PHP_EOL : '';
        $softDeleteRoutes .= 'Route::post(\''.$this->kebab_case_plural.'/{id}/restore'.'\',[\\App\Http\Controllers\\'.($folder_name ? $folder_name."\\" : '').$name.'Controller::class,'.'\'restore'.'\'])->name(\''.$this->kebab_case_plural.'.restore\');'.PHP_EOL;
        $softDeleteRoutes .= 'Route::post(\''.$this->kebab_case_plural.'/{id}/force-delete'.'\',[\\App\Http\Controllers\\'.($folder_name ? $folder_name."\\" : '').$name.'Controller::class,'.'\'forceDelete'.'\'])->name(\''.$this->kebab_case_plural.'.force_delete\');'.PHP_EOL;
        $softDeleteRoutes .= 'Route::resource(\''.$this->kebab_case_plural."',\\App\Http\Controllers\\".($folder_name ? $folder_name."\\" : '').$name."Controller::class);".PHP_EOL;
        $softDeleteRoutes .= !is_null($folder_name) ? '});'.PHP_EOL : '';

        $standardRoutes = !is_null($folder_name) ? "Route::group(['middleware' => 'auth', 'prefix' => '$formatted_folder_name', 'as' => '$formatted_folder_name.'], function () {".PHP_EOL : '';
        $standardRoutes .= $hasStatusField ? 'Route::get(\'status-change-'.$this->kebab_case_singular."',[\\App\Http\Controllers\\".($folder_name ? $folder_name."\\" : '').$name."Controller::class,'changeStatus'])->name("."'status-change-".$this->kebab_case_singular."');".PHP_EOL : '';
        $standardRoutes .= 'Route::resource(\''.$this->kebab_case_plural."',\\App\Http\Controllers\\".($folder_name ? $folder_name."\\" : '').$name."Controller::class);".PHP_EOL;
        $standardRoutes .= !is_null($folder_name) ? '});'.PHP_EOL : '';

        if ($hasSoftDeletes) {
            File::append($routePath, $softDeleteRoutes);
        } else {
            File::append($routePath, $standardRoutes);
        }

        if ($folder_name) {
            $this->generateControllerStub($name, $folder_name, $fields);
            $this->generateRequestStub($name, $folder_name, $fields);
            $this->generateModelStub($name, $folder_name, $fields, $relations);
            $this->generateBladeStub($name, $folder_name, $fields);
        } else {
            $this->generateControllerStub($name, null, $fields);
            $this->generateRequestStub($name, null, $fields);
            $this->generateModelStub($name, null, $fields, $relations);
            $this->generateBladeStub($name, null, $fields);
        }

        // Generate test if fields are present and user agrees
        /* if ($fields != '' && $this->confirm('Do you wish to generate Test?')) {
             if ($folder_name) {
                 $this->namedFeatureTestStub($name, $folder_name, $fields);
             } else {
                 $this->featureTestStub($name, $fields);
             }
             $this->tableArray[] = ['Feature Test', '<info>Created</info>'];
         }*/

        $this->tableArray = [['Model', '<info>Created</info>'], ['Controller', '<info>Created</info>'], ['Migration', '<info>Created</info>'], ['Form Request', '<info>Created</info>'], ['Blade Files', '<info>Created</info>']];
    }

    protected function publishComponents()
    {
        if (!file_exists($path = resource_path('/views/components'))) {
            mkdir($path, 0777, true);
            \File::copyDirectory(__DIR__.'/../components', resource_path("/views/components"));
        }
    }

    # STUBS -------------------------------------------------------------------------------------------------------

    protected function getStub($type)
    {
        return file_get_contents("$this->stub_path/crud_stubs/$type.stub");
    }

    protected function getBladeStub($type)
    {
        return file_get_contents("$this->stub_path/blade/$type.stub");
    }

    protected function generateMigrationStub($name, $fields = '')
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

    protected function generateRequestStub($name, $folder_name, $fields)
    {
        $validationRulesStore = $this->resolve_request($fields, 'store');
        $validationRulesUpdate = $this->resolve_request($fields, 'update');

        $storeTemplate = str_replace(
            [
                '{{modelName}}',
                '{{folderName}}',
                '{{validationRules}}'
            ],
            [
                $name,
                $folder_name ?? '',
                $validationRulesStore
            ],
            $this->getStub($folder_name ? 'named_request' : 'request')
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
                $validationRulesUpdate
            ],
            $this->getStub($folder_name ? 'named_update_request' : 'request_update')
        );

        // Create file dir if it does not exist
        $path = app_path("/Http/Requests/").($folder_name ? "/{$folder_name}" : '');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Update placeholder_model with valued Model
        file_put_contents("{$path}/{$name}StoreRequest.php", $storeTemplate);
        file_put_contents("{$path}/{$name}UpdateRequest.php", $updateTemplate);
    }

    private function generateModelStub($name, $folder_name, $fields, $relations)
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

    private function generateBladeStub($name, $folder_name, $fields)
    {
        $fieldHasImage = $this->has_image_field($fields);

        if ($this->hasSoftDeletes() == true) {
            $index_stub = $this->getBladeStub('trashed_blade');
        } else {
            $index_stub = $this->getBladeStub('index_blade');
        }

        $statusChangeHelperCode = $this->get_status_change_helper_code($fields);
        $fieldsForCreate = $this->get_fields_for_create($fields);
        $fieldsForEdit = $this->get_fields_for_edit($fields, $this->snake_case); //snake_case -> modelName
        $rows_for_index = $this->get_rows_for_index($fields, $this->snake_case);
        $thead_for_index = $this->get_thead_for_index($fields);
        $fieldsForShow = $this->get_fields_for_show($fields, $this->snake_case);

        $indexTemplate = str_replace(
            [
                '{{statusChangeHelperCode}}',
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralKebabCase}}',
                '{{modelNameSingularKebabCase}}',
                '{{folderName}}',
                '{{folderNameWithoutDot}}',
                '{{rowsForIndex}}',
                '{{theadForIndex}}',
            ],
            [
                $statusChangeHelperCode,
                $name,
                $this->snake_case,
                $this->snake_case_plural,
                $this->kebab_case_plural,
                $this->kebab_case_singular,
                $folder_name ? Str::snake($folder_name).'.' : '',
                $folder_name ? Str::snake($folder_name) : '',
                $rows_for_index,
                $thead_for_index,
            ],
            $index_stub
        );

        $createTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralKebabCase}}',
                '{{folderName}}',
                '{{folderNameWithSlash}}',
                '{{fieldsForCreate}}',
                '{{imageHelperCode}}',
            ],
            [
                $name,
                $this->kebab_case_plural,
                $folder_name ? Str::snake($folder_name).'.' : '',
                $folder_name ? $folder_name.'\\' : '',
                $fieldsForCreate,
                $fieldHasImage['imageHelperCode'],
                //$fieldsForCreateEdit // just uncomment to use it
            ],
            $this->getBladeStub('create_blade')
        );

        $editTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{folderName}}',
                '{{folderNameWithSlash}}',
                '{{modelNamePluralKebabCase}}',
                '{{fieldsForEdit}}',
                '{{imageHelperCode}}',
            ],
            [
                $name,
                $this->snake_case,
                $folder_name ? Str::snake($folder_name).'.' : '',
                $folder_name ? $folder_name.'\\' : '',
                $this->kebab_case_plural,
                $fieldsForEdit,
                $fieldHasImage['imageHelperCode'],
            ],
            $this->getBladeStub('edit_blade')
        );

        $showTemplate = str_replace(
            [
                '{{fieldsForShow}}'
            ],
            [
                $fieldsForShow
            ],
            $this->getBladeStub('show_blade')
        );

        //create folder if it does not exist
        $path = base_path("/resources/views".($folder_name ? "/".Str::snake($folder_name) : "")."/".$this->snake_case);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        //create file
        file_put_contents("{$path}/index.blade.php", $indexTemplate);
        file_put_contents("{$path}/create.blade.php", $createTemplate);
        file_put_contents("{$path}/edit.blade.php", $editTemplate);
        file_put_contents("{$path}/show.blade.php", $showTemplate);
    }

    private function generateControllerStub($name, $folder_name, $fields)
    {
        $methodCodes = $this->generate_controller_method_codes($name, $fields);

        $controller_stub = $this->getStub('controller');

        $fieldsForSelect = $this->get_select_query_fields_for_index($fields);

        $statusChangeMethodCode = $this->get_status_change_method_code($name, $fields);

        if ($this->hasSoftDeletes() == true) {
            $controller_stub = $this->getStub('soft_delete_controller');
        }

        if ($folder_name) {
            $controller_stub = $this->getStub('named_controller');

            if ($this->hasSoftDeletes() == true) {
                $controller_stub = $this->getStub('soft_delete_named_controller');
            }
        }

        $controllerTemplate = str_replace(
            [
                '{{statusChangeMethodCode}}',
                '{{statusChangeTrait}}',
                '{{statusChangeTraitImport}}',
                '{{folderName}}',
                '{{folderNameSnakeCase}}',
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralKebabCase}}',
                '{{storeMethodCode}}',
                '{{updateMethodCode}}',
                '{{deleteMethodCode}}',
                '{{fieldsForSelect}}',
            ],
            [
                $statusChangeMethodCode['statusChangeMethodCode'],
                $statusChangeMethodCode['statusChangeTrait'],
                $statusChangeMethodCode['statusChangeTraitImport'],
                $folder_name ?? '',
                $folder_name ? Str::snake($folder_name) : '',
                $name,
                $this->snake_case,
                $this->snake_case_plural,
                $this->kebab_case_plural,
                $methodCodes['store'],
                $methodCodes['update'],
                $methodCodes['delete'],
                $fieldsForSelect,
            ],
            $controller_stub
        );

        // Create folder if it does not exist
        if ($folder_name && !file_exists($path = app_path("/Http/Controllers/{$folder_name}"))) {
            mkdir($path, 0777, true);
        }

        // Update placeholder_model with valued Model
        $filePath = $folder_name ? "{$folder_name}/{$name}Controller.php" : "{$name}Controller.php";
        file_put_contents(app_path("/Http/Controllers/{$filePath}"), $controllerTemplate);
    }

    # END OF STUBS -------------------------------------------------------------------------------------------------------

    protected function featureTestStub($name, $fields = '')
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

    protected function namedFeatureTestStub($name, $folder_name, $fields = '')
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

    protected function hasSoftDeletes(): bool
    {
        return is_bool($this->option('softDelete')) && $this->option('softDelete') === true;
    }
}
