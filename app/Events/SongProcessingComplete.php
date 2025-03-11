<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SongProcessingComplete implements ShouldBroadcast
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
     * İşlemin başarılı olup olmadığı
     *
     * @var bool
     */
    public $success;

    /**
     * Sonuç mesajı veya hata
     *
     * @var mixed
     */
    public $result;

    /**
     * Tenant ID
     *
     * @var int|null
     */
    public $tenantId;

    /**
     * Şarkı detayları (kalite analizi sonuçları dahil)
     *
     * @var array|null
     */
    public $details;

    /**
     * Create a new event instance.
     *
     * @param int $productId
     * @param string $fileName
     * @param bool $success
     * @param mixed $result
     * @param int|null $tenantId
     * @param array|null $details
     * @return void
     */
    public function __construct(int $productId, string $fileName, bool $success, $result = null, ?int $tenantId = null, ?array $details = null)
    {
        $this->productId = $productId;
        $this->fileName = $fileName;
        $this->success = $success;
        $this->result = $result;
        $this->tenantId = $tenantId ?? (tenant() ? tenant()->id : null);
        $this->details = $details;
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
            'status' => $this->success ? 'completed' : 'failed',
            'success' => $this->success,
            'result' => $this->result,
            'message' => $this->success ? 'Dosya işleme tamamlandı' : 'Dosya işleme başarısız oldu',
            'timestamp' => now()->toIso8601String(),
            'details' => $this->details,
        ];
    }
}
