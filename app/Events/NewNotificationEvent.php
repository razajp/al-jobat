<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewNotificationEvent implements ShouldBroadcast
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new Channel('notifications'); // Note: public channel
    }
}