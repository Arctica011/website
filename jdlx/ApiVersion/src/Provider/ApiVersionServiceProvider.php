<?php

namespace Jdlx\ApiVersion\Provider;

use App\Library\ApiVersion\ApiVersion;
use Illuminate\Support\ServiceProvider;

class   ApiVersionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('apiversion',function(){

            return new ApiVersion();

        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
