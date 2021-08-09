<?php

namespace Niraj\CrudStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Niraj\CrudStarter\traits\tableTrait;

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

        //define path

        $this->model_path = app_path("/Models/{$name}.php");
        $this->request_path = app_path("/Http/Requests/{$name}ApiRequest.php");
        $this->test_path = base_path("/tests/Feature/{$name}ApiTest.php");
        $this->controller_path = app_path("/Http/Controllers/Api/{$name}ApiController.php");
        $this->resource_path = app_path("/Http/Resources/{$name}Resource.php");

        $this->is_valid_path();
    }

    //checks if path/folder name is valid

    protected function is_valid_path()
    {
        if (file_exists($this->request_path) && file_exists($this->controller_path) && file_exists($this->resource_path)) {

            $this->tableArray = [['Api Controller', '<info>deleted</info>'], ['Resource', '<info>deleted</info>'], ['Form Request', '<info>deleted</info>']];

            if (file_exists($this->model_path) && $this->confirm('Model Detected Want to Delete it too ?')) {
                $this->delete_model();
                $this->tableArray [] = ['Model', '<info>deleted</info>'];
            }

            //call delete functions
            $this->delete_request();
            $this->delete_controller();
            $this->delete_resource_path();
            $this->delete_test();

            $this->showTableInfo($this->tableArray,'API Files deleted');
            $this->warn('Please remove migration manually!!');
        } else {
            $this->error('Failed to Delete , Make sure File exist, FileName is correct or Folder is Named');
        }
    }

    //functions to delete duh !

    protected function delete_request()
    {
        \File::delete($this->request_path);
    }

    protected function delete_model()
    {
        \File::delete($this->model_path);
    }

    protected function delete_controller()
    {
        \File::delete($this->controller_path);
    }

    protected function delete_resource_path()
    {
        \File::delete($this->resource_path);
    }

    protected function delete_test()
    {
        \File::delete($this->test_path);
    }
}
