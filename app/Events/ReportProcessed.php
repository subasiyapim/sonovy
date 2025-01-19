<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $result;

    /**
     * Create a new event instance.
     */
    public function __construct($result)
    {
        $this->result = $result;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('reportProcessed.'.auth()->id()),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reportProcessed';
    }
}
