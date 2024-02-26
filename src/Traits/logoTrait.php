<?php


namespace Niraj\CrudStarter\Traits;


trait logoTrait
{
    protected function show_logo()
    {
        $logo = "
   █▀▀ █▀█ █░█ █▀▄   █▀ ▀█▀ ▄▀█ █▀█ ▀█▀ █▀▀ █▀█
   █▄▄ █▀▄ █▄█ █▄▀   ▄█ ░█░ █▀█ █▀▄ ░█░ ██▄ █▀▄";


        $this->line($logo);
    }
}
