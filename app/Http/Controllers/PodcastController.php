<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Jobs\ProcessPodcast;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PodcastController extends Controller
{

    /**
     * 保存播客。
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // 创建播客...

        ProcessPodcast::dispatch($podcast);

        //任务在分配后 10 分钟内不可被处理：
        ProcessPodcast::dispatch($podcast)
                ->delay(Carbon::now()->addMinutes(10));

        ProcessPodcast::withChain([
            new OptimizePodcast,
            new ReleasePodcast
        ])->dispatch();
        
        //分发任务到指定队列
        ProcessPodcast::dispatch($podcast)->onQueue('processing');
        
        //分发任务到指定连接
        ProcessPodcast::dispatch($podcast)->onConnection('sqs');
        
        ProcessPodcast::dispatch($podcast)
              ->onConnection('sqs')
              ->onQueue('processing');
    }

}
