<?php

namespace Illuminate\Notifications\Messages;

class DatabaseMessage
{
    /**
     * 这个通知的数据将被存储
     *
     * @var array
     */
    public $data = [];

    /**
     * 创建一个新的数据消息实例
     *
     * @param  array  $data
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
}
