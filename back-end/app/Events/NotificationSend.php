<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSend implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $count;
    public $id;

    public function __construct($count, $id)
    {
        $this->count = $count;
        $this->id = $id;
    }
  
    public function broadcastOn()
    {
        return ['notiflication-channel'];
    }
  
    public function broadcastAs()
    {
        return 'NotificationSend';
    }
}
