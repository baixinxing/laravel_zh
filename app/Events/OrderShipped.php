<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Queue\SerializesModels;

//订单出货事件
class OrderShipped
{
    use SerializesModels;

    public $order;
    
    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
