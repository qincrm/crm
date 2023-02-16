<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RightService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(RightService::class, function ($app) {
            return new RightService();
        });
    }

    public function boot() {
        error_reporting(E_ALL ^ E_NOTICE);//
    }
}
