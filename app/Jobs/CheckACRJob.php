<?php

namespace App\Jobs;

use App\Models\Broadcast;
use App\Models\Setting;
use App\Services\ACRServices;
use App\Services\FFMpegServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CheckACRJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Broadcast $broadcast;

    public $trim_audio_start_time;
    public $trim_audio_end_time;
    public $trim_video_start_time;
    public $trim_video_end_time;

    /**
     * Create a new job instance.
     */
    public function __construct(Broadcast $broadcast)
    {
        $this->broadcast = $broadcast;

        $settings = Cache::remember('settings', 3600, function () {
            return Setting::whereIn('key', ['trim_video_start_time', 'trim_video_end_time', 'trim_audio_start_time', 'trim_audio_end_time'])
                ->get()
                ->pluck('value', 'key');
        });

        $this->trim_audio_start_time = $settings['trim_audio_start_time'];
        $this->trim_audio_end_time = $settings['trim_audio_end_time'];
        $this->trim_video_start_time = $settings['trim_video_start_time'];
        $this->trim_video_end_time = $settings['trim_video_end_time'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        if (!File::exists(public_path() . '/storage/songs/samples')) {
            File::makeDirectory(public_path() . '/storage/songs/samples', 0777, true, true);
        }

        foreach ($this->broadcast->songs as $song) {

            //$file = Storage::get('public/' . $song->path);
            $file = public_path() . '/storage/' . $song->path;
            $file_name = public_path() . '/storage/songs/samples/' . explode('/', $song->path)[1];
            $storage_file = 'public/songs/samples/' . explode('/', $song->path)[1];
            if ($song->type == 1) {
                $trimmed = FFMpegServices::trimAudio($file, $file_name, $this->trim_audio_start_time, $this->trim_audio_end_time);
            } elseif ($song->type == 2) {
                $trimmed = FFMpegServices::trimVideo($file, $file_name, $this->trim_video_start_time, $this->trim_video_end_time);
            } else {
                $trimmed = FFMpegServices::trimAudio($file, $file_name, $this->trim_audio_start_time, $this->trim_audio_end_time);
            }

            if ($trimmed['status'] !== false) {
                //$sample = Storage::get($file_name);
                $acr_response = ACRServices::identify($storage_file);
                if ($acr_response->ok()) {

                    $song->acr_response = $acr_response->json();
                    $song->save();
                }
            }
        }
    }
}
