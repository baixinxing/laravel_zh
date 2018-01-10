<?php

namespace Illuminate\Log;

use Monolog\Logger as Monolog;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * 注册服务提供者
     *
     * @return void
     */
    public function register()
    {
        //共享绑定log
        $this->app->singleton('log', function () {
            return $this->createLogger();
        });
    }

    /**
     * 创建记录器
     *
     * @return \Illuminate\Log\Writer
     */
    public function createLogger()
    {
        $log = new Writer(
            new Monolog($this->channel()), $this->app['events']
        );
        
        //设置处理器 优先调用自定义配置程序 否则调用自身配置程序
        if ($this->app->hasMonologConfigurator()) {
            call_user_func($this->app->getMonologConfigurator(), $log->getMonolog());
        } else {
            $this->configureHandler($log);
        }
        
        //返回记录器实例
        return $log;
    }

    /**
     * 获取日志“频道”的名称。
     *
     * @return string
     */
    protected function channel()
    {
        if ($this->app->bound('config') &&
            $channel = $this->app->make('config')->get('app.log_channel')) {
            return $channel;
        }

        return $this->app->bound('env') ? $this->app->environment() : 'production';
    }

    /**
     * 配置应用程序的Monolog处理程序。
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureHandler(Writer $log)
    {
        // 默认 configureSingleHandler
        $this->{'configure'.ucfirst($this->handler()).'Handler'}($log);
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureSingleHandler(Writer $log)
    {
        $log->useFiles(
            $this->app->storagePath().'/logs/laravel.log',
            $this->logLevel()
        );
    }

    /**
     * 配置应用程序的Monolog处理程序。
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureDailyHandler(Writer $log)
    {
        $log->useDailyFiles(
            $this->app->storagePath().'/logs/laravel.log', $this->maxFiles(),
            $this->logLevel()
        );
    }

    /**
     * 配置应用程序的Monolog处理程序。
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureSyslogHandler(Writer $log)
    {
        $log->useSyslog('laravel', $this->logLevel());
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureErrorlogHandler(Writer $log)
    {
        $log->useErrorLog($this->logLevel());
    }

    /**
     * 获取默认日志处理程序。
     *
     * @return string
     */
    protected function handler()
    {
        if ($this->app->bound('config')) {
            return $this->app->make('config')->get('app.log', 'single');
        }

        return 'single';
    }

    /**
     * Get the log level for the application.
     *
     * @return string
     */
    protected function logLevel()
    {
        if ($this->app->bound('config')) {
            return $this->app->make('config')->get('app.log_level', 'debug');
        }

        return 'debug';
    }

    /**
     * Get the maximum number of log files for the application.
     *
     * @return int
     */
    protected function maxFiles()
    {
        if ($this->app->bound('config')) {
            return $this->app->make('config')->get('app.log_max_files', 5);
        }

        return 0;
    }
}
