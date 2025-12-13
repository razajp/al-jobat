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
        // Check Pusher flag
        if (!app('pusher.enabled')) {
            return []; // Return empty array → event will not broadcast
        }

        return new Channel('notifications'); // Public channel
    }
}
