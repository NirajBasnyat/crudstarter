<?php


namespace Niraj\CrudStarter\Traits;


trait generalHelperTrait
{
    public function getInstalledLaravelVersion(): int
    {
        return (int) app()->version();
    }
}
