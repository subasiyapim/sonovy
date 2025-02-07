<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $result;
    private string $tenantId;
    private string $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($result, int $tenantId, int $userId)
    {
        $this->result = $result;
        $this->tenantId = $tenantId;
        $this->userId = $userId;
    }

    public function broadcastOn(): Channel
    {
        $channelName = "tenant.{$this->tenantId}.reportProcessed.{$this->userId}";

        return new PrivateChannel($channelName);
    }

    public function broadcastAs(): string
    {
        return 'reportProcessed';
    }
}
