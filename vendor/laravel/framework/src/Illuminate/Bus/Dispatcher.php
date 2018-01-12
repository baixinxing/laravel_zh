<?php

namespace Illuminate\Bus;

use Closure;
use RuntimeException;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Bus\QueueingDispatcher;

class Dispatcher implements QueueingDispatcher
{
    /**
     * The container implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The pipeline instance for the bus.
     *
     * @var \Illuminate\Pipeline\Pipeline
     */
    protected $pipeline;

    /**
     * The pipes to send commands through before dispatching.
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * The command to handler mapping for non-self-handling events.
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * 队列解析器回调。
     *
     * @var \Closure|null
     */
    protected $queueResolver;

    /**
     * Create a new command dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @param  \Closure|null  $queueResolver
     * @return void
     */
    public function __construct(Container $container, Closure $queueResolver = null)
    {
        $this->container = $container;
        $this->queueResolver = $queueResolver;
        $this->pipeline = new Pipeline($container);
    }

    /**
     * 将一个命令分发给相应的处理程序。
     *
     * @param  mixed  $command
     * @return mixed
     */
    public function dispatch($command)
    {
        //Queue 5 命令分发
        if ($this->queueResolver && $this->commandShouldBeQueued($command)) {
            //Queue 7 分发到队列中
            return $this->dispatchToQueue($command);
        }

        return $this->dispatchNow($command);
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * @param  mixed  $command
     * @param  mixed  $handler
     * @return mixed
     */
    public function dispatchNow($command, $handler = null)
    {
        if ($handler || $handler = $this->getCommandHandler($command)) {
            $callback = function ($command) use ($handler) {
                return $handler->handle($command);
            };
        } else {
            $callback = function ($command) {
                return $this->container->call([$command, 'handle']);
            };
        }

        return $this->pipeline->send($command)->through($this->pipes)->then($callback);
    }

    /**
     * Determine if the given command has a handler.
     *
     * @param  mixed  $command
     * @return bool
     */
    public function hasCommandHandler($command)
    {
        return array_key_exists(get_class($command), $this->handlers);
    }

    /**
     * Retrieve the handler for a command.
     *
     * @param  mixed  $command
     * @return bool|mixed
     */
    public function getCommandHandler($command)
    {
        if ($this->hasCommandHandler($command)) {
            return $this->container->make($this->handlers[get_class($command)]);
        }

        return false;
    }

    /**
     * 确定给定的命令是否应该排队。
     *
     * @param  mixed  $command
     * @return bool
     */
    protected function commandShouldBeQueued($command)
    {
        //Queue 6 判断是否应该排队实例
        return $command instanceof ShouldQueue;
    }

    /**
     * 将一个命令分发给队列后面的相应处理程序。
     *
     * @param  mixed  $command
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function dispatchToQueue($command)
    {
        $connection = $command->connection ?? null;

        $queue = call_user_func($this->queueResolver, $connection);

        if (! $queue instanceof Queue) {
            throw new RuntimeException('Queue resolver did not return a Queue implementation.');
        }

        if (method_exists($command, 'queue')) {
            return $command->queue($queue, $command);
        }
        
        //
        return $this->pushCommandToQueue($queue, $command);
    }

    /**
     * 将命令推送到给定的队列实例上。
     *
     * @param  \Illuminate\Contracts\Queue\Queue  $queue
     * @param  mixed  $command
     * @return mixed
     */
    protected function pushCommandToQueue($queue, $command)
    {
        if (isset($command->queue, $command->delay)) {
            return $queue->laterOn($command->queue, $command->delay, $command);
        }

        if (isset($command->queue)) {
            return $queue->pushOn($command->queue, $command);
        }

        if (isset($command->delay)) {
            return $queue->later($command->delay, $command);
        }

        return $queue->push($command);
    }

    /**
     * Set the pipes through which commands should be piped before dispatching.
     *
     * @param  array  $pipes
     * @return $this
     */
    public function pipeThrough(array $pipes)
    {
        $this->pipes = $pipes;

        return $this;
    }

    /**
     * Map a command to a handler.
     *
     * @param  array  $map
     * @return $this
     */
    public function map(array $map)
    {
        $this->handlers = array_merge($this->handlers, $map);

        return $this;
    }
}
