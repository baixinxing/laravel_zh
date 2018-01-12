<?php

namespace Illuminate\Foundation\Bus;

use Illuminate\Contracts\Bus\Dispatcher;

trait DispatchesJobs
{
    /**
     * 派遣一个工作，以适当的处理程序。
     *
     * @param  mixed  $job
     * @return mixed
     */
    protected function dispatch($job)
    {
        //Queue 4 命令分发
        return app(Dispatcher::class)->dispatch($job);
    }

    /**
     * Dispatch a job to its appropriate handler in the current process.
     *
     * @param  mixed  $job
     * @return mixed
     */
    public function dispatchNow($job)
    {
        return app(Dispatcher::class)->dispatchNow($job);
    }
}
