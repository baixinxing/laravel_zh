<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * 定义全局模式
     * 定义您的路由模型绑定，模式过滤器等
     *
     * @return void
     */
    public function boot()
    {
        Route::pattern('id', '[0-9]+');
        Route::model('user', 'App\Model\User');
        parent::boot();
    }

    /**
     * 确定应用程序的路由
     *
     * @return void
     */
    public function map()
    {
        //API路由
        $this->mapApiRoutes();

        //web路由
        $this->mapWebRoutes();

        //
    }

    /**
     * 确定应用程序的web路由
     *
     * 这些路由接收session状态，CSRF保护等
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * 定义应用的API路由
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
