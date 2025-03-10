<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SongProcessingStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Ürün ID
     *
     * @var int
     */
    public $productId;

    /**
     * Dosya adı
     *
     * @var string
     */
    public $fileName;

    /**
     * Tenant ID
     *
     * @var int|null
     */
    public $tenantId;

    /**
     * Create a new event instance.
     *
     * @param int $productId
     * @param string $fileName
     * @param int|null $tenantId
     * @return void
     */
    public function __construct(int $productId, string $fileName, ?int $tenantId = null)
    {
        $this->productId = $productId;
        $this->fileName = $fileName;
        $this->tenantId = $tenantId ?? (tenant() ? tenant()->id : null);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Tenant bazlı kanal adı oluştur, tenant ID yoksa normal kanal adı kullan
        if ($this->tenantId) {
            return new PrivateChannel('tenant.'.$this->tenantId.'.song.processing.'.$this->productId);
        }

        return new PrivateChannel('song.processing.'.$this->productId);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'product_id' => $this->productId,
            'file_name' => $this->fileName,
            'tenant_id' => $this->tenantId,
            'status' => 'started',
            'message' => 'Dosya işleme başladı',
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
