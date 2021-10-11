<?php

namespace Jdlx;

use Jdlx\Commands\Account\AccountChangePassword;
use Jdlx\Commands\Account\AccountCreate;
use Jdlx\Commands\Auth\LoginPassport;
use Jdlx\Commands\Auth\LoginSanctum;
use Jdlx\Commands\Crud\GenerateCrudCommand;
use Jdlx\Commands\Crud\ScaffoldComponentCrudCommand;
use Jdlx\Commands\Crud\ScaffoldPageCrudCommand;
use Jdlx\Commands\Crud\ScaffoldRouterCrudCommand;
use Jdlx\Commands\Make\MakeApi;
use Jdlx\Commands\Make\MakeApiController;
use Jdlx\Commands\Make\MakeApiFactory;
use Jdlx\Commands\Make\MakeApiFront;
use Jdlx\Commands\Make\ApiModelMakeCommand;
use Jdlx\Commands\Make\MakeApiModel;
use Jdlx\Commands\Make\MakeApiResource;
use Jdlx\Commands\Make\MakeApiTestCase;
use Illuminate\Support\ServiceProvider;

class JdlxServiceProvider extends ServiceProvider
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
                GenerateCrudCommand::class,
                ScaffoldPageCrudCommand::class,
                ScaffoldComponentCrudCommand::class,
                ScaffoldRouterCrudCommand::class,
                AccountChangePassword::class,
                AccountCreate::class,
                LoginPassport::class,
                LoginSanctum::class,
                AccountCreate::class,
                MakeApi::class,
                MakeApiController::class,
                MakeApiFactory::class,
                MakeApiFront::class,
                MakeApiModel::class,
                MakeApiResource::class,
                MakeApiTestCase::class
            ]);
        }


        $this->publishes([
            __DIR__.'/../publish/resources/views' => resource_path('views/vendor/jdlx'),
            __DIR__.'/../publish/app' => base_path('app'),
            __DIR__.'/../publish/routes' => base_path('routes'),
            __DIR__.'/../publish/webclient' => base_path('webclient'),
            __DIR__.'/../publish/resources' => base_path('resources'),
            __DIR__.'/../publish/package.json' => base_path('package.json'),
            __DIR__.'/../publish/package-lock.json' => base_path('package-lock.json'),
            __DIR__.'/../publish/tests' => base_path('tests'),
        ]);
    }



}
