<?php

namespace Niraj\CrudStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Niraj\CrudStarter\Traits\tableTrait;

class DeleteApi extends Command
{
    use tableTrait;

    protected $signature = "del:api {name}";

    protected $description = 'Deletes Generated API Files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->argument('name');

        $this->tableArray = array();

        //define variable

        $this->snake_case = Str::snake($name);
        $this->snake_case_plural = Str::plural(Str::snake($name));

        //define path

        if ($this->confirm('Are API files place inside specific folder?')) {

            $folder_name = $this->ask('Enter the folder name (case sensitive)');

            $this->controller_path = app_path("/Http/Controllers/{$folder_name}Api/{$name}ApiController.php");
            $this->store_request_path = app_path("/Http/Requests/{$folder_name}Api/{$name}StoreApiRequest.php");
            $this->update_request_path = app_path("/Http/Requests/{$folder_name}Api/{$name}UpdateApiRequest.php");
            $this->resource_path = app_path("Http/Resources/{$folder_name}/{$name}Resource.php");

        } else {
            $this->controller_path = app_path("/Http/Controllers/Api/{$name}ApiController.php");
            $this->store_request_path = app_path("/Http/Requests/Api/{$name}StoreApiRequest.php");
            $this->update_request_path = app_path("/Http/Requests/Api/{$name}UpdateApiRequest.php");
            $this->resource_path = app_path("Http/Resources/{$name}Resource.php");
        }

        $this->model_path = app_path("/Models/{$name}.php");

        $this->is_valid_path();
    }

    //checks if path/folder name is valid

    protected function is_valid_path()
    {
        if (file_exists($this->model_path) && file_exists($this->store_request_path) && file_exists($this->update_request_path) && file_exists($this->controller_path) && file_exists($this->resource_path)) {

            $this->tableArray = [['Model', '<info>deleted</info>'], ['Form Request', '<info>deleted</info>'], ['Controller', '<info>deleted</info>'], ['Resource', '<info>deleted</info>']];

            //call delete functions
            $this->delete_model();
            $this->delete_controller();
            $this->delete_request();
            $this->delete_resource_path();

            $this->showTableInfo($this->tableArray, 'CRUD Files deleted');
            $this->warn('Please remove migration and web routes manually!!');
        } else {
            $this->error('Failed to Delete , Make sure File exist, FileName is correct or Folder is Named');
        }
    }

    //functions to delete duh !

    protected function delete_model()
    {
        \File::delete($this->model_path);
    }

    protected function delete_controller()
    {
        \File::delete($this->controller_path);
    }

    protected function delete_request()
    {
        \File::delete($this->store_request_path);
        \File::delete($this->update_request_path);
    }

    protected function delete_resource_path()
    {
        \File::delete($this->resource_path);
    }
}
