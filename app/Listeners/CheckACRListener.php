<?php

namespace App\Listeners;

use App\Events\NewBroadcastEvent;
use App\Jobs\CheckACRJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
    public function handle(NewBroadcastEvent $event): void
    {

        CheckACRJob::dispatch($event->broadcast);
    }
}
