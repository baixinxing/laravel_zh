<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class AppServiceProvider extends ServiceProvider
{
    /**
     * 启动任意应用程序的服务。
     *
     * @return void
     */
    public function boot()
    {
        Queue::looping(function () {
            while (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
        });
        
        Queue::before(function (JobProcessing $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
        });

        Queue::after(function (JobProcessed $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
        });
        
        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception
        });
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
