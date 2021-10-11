<?php


namespace Jdlx\ApiVersion\Facade;


use Illuminate\Support\Facades\Facade;

class Version extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'apiversion';
    }
}
