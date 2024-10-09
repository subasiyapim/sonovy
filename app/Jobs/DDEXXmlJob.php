<?php

namespace App\Jobs;

use App\Enums\ProductTypeEnum;
use App\Models\Product;
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

    protected $product;

    /**
     * Create a new job instance.
     *
     * @param  Product  $product
     */
    public function __construct(Product $product)
    {
        $this->broadcast = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch (intval($this->broadcast->type->value)) {
            case ProductTypeEnum::SOUND->value:
                DDEXService::makeAudioXml($this->broadcast);
                break;
            case ProductTypeEnum::VIDEO->value:
                DDEXService::makeVideoXml($this->broadcast);
                break;
            case ProductTypeEnum::RINGTONE->value:
                DDEXService::makeRingToneXml($this->broadcast);
                break;
            default:
                Log::error('Invalid broadcast type');
                break;
        }
    }
}
