<?php

namespace Jdlx;

use Jdlx\Commands\Account\AccountChangePassword;
use Jdlx\Commands\Account\AccountCreate;
use Jdlx\Commands\Auth\LoginPassport;
use Jdlx\Commands\Auth\LoginSanctum;
use Jdlx\Commands\Make\MakeApi;
use Jdlx\Commands\Make\MakeApiController;
use Jdlx\Commands\Make\MakeApiFactory;
use Jdlx\Commands\Make\MakeApiFront;
use Jdlx\Commands\Make\ApiModelMakeCommand;
use Jdlx\Commands\Make\MakeApiResource;
use Jdlx\Commands\Make\MakeApiTestCase;
use Illuminate\Support\ServiceProvider;

class JdlxTasklogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([

            ]);
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }



}
