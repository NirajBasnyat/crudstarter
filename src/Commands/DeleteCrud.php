<?php

namespace Niraj\CrudStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Niraj\CrudStarter\Traits\tableTrait;

class DeleteCrud extends Command
{
    use tableTrait;

    protected $signature = "del:crud {name}";

    protected $description = 'Deletes Generated CRUD Files';

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
        $this->test_path = base_path("/tests/Feature/{$name}Test.php");

        if ($this->confirm('Are CRUD files Placed inside Specific Folder?')) {

            $folder_name = $this->ask('Enter the Folder Name');

            $this->controller_path = app_path("/Http/Controllers/{$folder_name}/{$name}Controller.php");
            $this->model_path = app_path("/Models/{$folder_name}/{$name}.php");
            $this->blade_folder = base_path("/resources/views/" . Str::snake($folder_name) . "/" . $this->snake_case);
            $this->request_path = app_path("/Http/Requests/{$folder_name}/{$name}Request.php");

            $this->is_valid_path();

        } else {
            $this->model_path = app_path("/Models/{$name}.php");
            $this->controller_path = app_path("/Http/Controllers/{$name}Controller.php");
            $this->blade_folder = base_path("/resources/views/" . $this->snake_case);
            $this->request_path = app_path("/Http/Requests/{$name}Request.php");

            $this->is_valid_path();
        }
    }

    //checks if path/folder name is valid

    protected function is_valid_path()
    {
        if (file_exists($this->model_path) && file_exists($this->request_path) && file_exists($this->controller_path) && file_exists($this->blade_folder)) {

            $this->tableArray = [['Model', '<info>deleted</info>'], ['Form Request', '<info>deleted</info>'], ['Controller', '<info>deleted</info>'], ['Blade Files', '<info>deleted</info>']];

            //call delete functions
            $this->delete_model();
            $this->delete_controller();
            $this->delete_blade_folder();
            $this->delete_request();
            $this->delete_test();

            $this->showTableInfo($this->tableArray,'CRUD Files deleted');
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

    protected function delete_blade_folder()
    {
        \File::deleteDirectory($this->blade_folder);
    }

    protected function delete_request()
    {
        \File::delete($this->request_path);
    }

    protected function delete_test()
    {
        \File::delete($this->test_path);
    }
}
