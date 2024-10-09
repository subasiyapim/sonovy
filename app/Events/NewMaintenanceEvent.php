<?php

namespace App\Events;

use App\Models\AnnouncementUser;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMaintenanceEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public AnnouncementUser $maintenance;


    /**
     * Create a new event instance.
     */
    public function __construct($maintenance)
    {
        $this->maintenance = $maintenance;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . auth()->id()),
        ];
    }
}
