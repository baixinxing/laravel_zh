<?php

namespace Illuminate\Notifications\Messages;

use Illuminate\Bus\Queueable;

class BroadcastMessage
{
    use Queueable;

    /**
     * 通知的数据
     *
     * @var array
     */
    public $data;

    /**
     * 创建一个新的消息实例
     *
     * @param  array  $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * 设置消息数据
     *
     * @param  array  $data
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }
}
