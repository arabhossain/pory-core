<?php


namespace Pory\Core\ServiceProviders;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Pory\Core\PoryLogs;

class PoryCoreServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app['router']->aliasMiddleware('pory-core', MiddlewareServices::class);

        Config::set('pory.modules.core', [
            'description'    => '',
            'active'    => true,
            'config'    => [
//                'dbConnection' => [
//                    'driver' => env('LOG_DB_CONNECTION'),
//                    'host' => env('LOG_DB_HOST'),
//                    'port' => env('LOG_DB_PORT'),
//                    'database' => env('LOG_DB_DATABASE'),
//                    'username' => env('LOG_DB_USERNAME'),
//                    'password' => env('LOG_DB_PASSWORD'),
//                ]
            ]

        ]);
    }

    public function register()
    {
//        $this->app->singleton('poryLogs', function () {
//            return new PoryLogs();
//        });
    }

}
