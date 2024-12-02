<?php

namespace App\Listeners;

use App\Events\NewProductEvent;
use App\Jobs\CheckACRJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CheckACRListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewProductEvent $event): void
    {
        if ($event->product->songs()->count() === 0) {
            Log::info('No songs found for product '.$event->product->id);
            return;
        }
        CheckACRJob::dispatch($event->product);
    }
}
