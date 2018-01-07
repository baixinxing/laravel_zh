<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * 引导任何应用程序服务。
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * 注册任何应用程序服务
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
    }
}
