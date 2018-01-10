<?php

namespace Illuminate\Log\Events;

class MessageLogged
{
    /**
     * 日志级别
     *
     * @var string
     */
    public $level;

    /**
     * 日志信息
     *
     * @var string
     */
    public $message;

    /**
     * 日志上下文
     *
     * @var array
     */
    public $context;

    /**
     * 创建一个新的事件实例
     *
     * @param  string  $level   日志级别
     * @param  string  $message 日志信息
     * @param  array  $context  日志上下文
     * @return void
     */
    public function __construct($level, $message, array $context = [])
    {
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;
    }
}
