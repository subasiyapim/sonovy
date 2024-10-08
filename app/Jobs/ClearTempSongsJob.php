<?php

namespace App\Jobs;

use App\Models\Song;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class ClearTempSongsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $songs;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->songs = Song::whereDate('created_at', '<', now()->subDays(1))
            ->whereNull('isrc')
            ->get();

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->songs->count() > 0) {
            foreach ($this->songs as $song) {

                if ($song->path && File::exists(public_path() . '/storage/' . $song->path))
                    unlink(public_path() . '/storage/' . $song->path);

                $song->delete();
            }
        }

    }
}
