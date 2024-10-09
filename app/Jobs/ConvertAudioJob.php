<?php

namespace App\Jobs;

use App\Enums\ConvertAudioEnum;
use App\Models\Product;
use App\Models\ConvertAudio;
use App\Models\Song;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class ConvertAudioJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Product $product;
    protected Song $song;
    protected ConvertAudio $convertAudio;

    protected $request;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;
    public $tries = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(Product $product, Song $song, $request)
    {
        $this->broadcast = $product;
        $this->song = $song;
        $this->convertAudio = ConvertAudio::create(
            [
                'product_id' => $product->id,
                'song_id' => $song->id,
                'output_file' => null,
                'error' => null,
                'release_date' => $request['release_date'],
                'timezone_id' => $request['timezone_id'],
                'release_time' => $request['release_time'],
                'channel_theme_id' => $request['channel_theme_id'],
                'status' => ConvertAudioEnum::PENDING
            ]
        );
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $song_file = storage_path('app/public/'.$this->song->path);
        $cover_image = $this->broadcast->image->url;

        if (!File::exists(storage_path('app/public/converted-songs'))) {
            File::makeDirectory(storage_path('app/public/converted-songs'), 0777, true, true);
        }

        $temp_output_file = storage_path('app/public/converted-songs/'.uniqid().'.mp4');

        try {
            $this->convertAudio->update(['status' => ConvertAudioEnum::CONVERTING]);

            $command = "ffmpeg -loop 1 -i $cover_image -i $song_file -c:v libx264 -c:a aac -strict -2 -b:a 192k -pix_fmt yuv420p -shortest $temp_output_file";

            $process = Process::fromShellCommandline($command);
            $process->setTimeout(null); // Zaman aşımını kapat
            $process->run();

            if ($process->isSuccessful()) {
                $output_file_path = uniqid().'.mp4';
                Storage::disk('converted-songs')->put($output_file_path, file_get_contents($temp_output_file));

                $output_url = Storage::disk('converted-songs')->url($output_file_path);

                $this->convertAudio->update(
                    [
                        'output_file' => $output_file_path,
                        'status' => ConvertAudioEnum::COMPLETED
                    ]
                );

                unlink($temp_output_file);
            } else {
                $this->convertAudio->update(
                    [
                        'status' => ConvertAudioEnum::FAILED,
                        'error' => $process->getErrorOutput()
                    ]
                );
                Log::error('FFMpeg Conversion Failed: '.$process->getErrorOutput());
            }
        } catch (\Exception $e) {
            $this->convertAudio->update(
                [
                    'status' => ConvertAudioEnum::FAILED,
                    'error' => $e->getMessage()
                ]
            );

            unlink($temp_output_file);
            Log::error('Error converting audio to video: '.$e->getMessage());
        }
    }
}
