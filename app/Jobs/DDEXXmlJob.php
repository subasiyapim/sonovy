<?php

namespace App\Jobs;

use App\Enums\BroadcastTypeEnum;
use App\Models\Broadcast;
use App\Services\DDEXService;
use DOMDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DDEXXmlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $broadcast;

    /**
     * Create a new job instance.
     *
     * @param Broadcast $broadcast
     */
    public function __construct(Broadcast $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch (intval($this->broadcast->type->value)) {
            case BroadcastTypeEnum::SOUND->value:
                DDEXService::makeAudioXml($this->broadcast);
                break;
            case BroadcastTypeEnum::VIDEO->value:
                DDEXService::makeVideoXml($this->broadcast);
                break;
            case BroadcastTypeEnum::RINGTONE->value:
                DDEXService::makeRingToneXml($this->broadcast);
                break;
            default:
                Log::error('Invalid broadcast type');
                break;
        }
    }
}
