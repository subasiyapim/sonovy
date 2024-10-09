<?php

namespace App\Listeners;

use App\Events\NewProductEvent;
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
    public function handle(NewProductEvent $event): void
    {

        CheckACRJob::dispatch($event->broadcast);
    }
}
