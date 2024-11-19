<?php

namespace App\Services;

use App\Enums\ProductTypeEnum;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Audio\Mp3;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FFMpegServices
{
    private static function ffprobe(): FFProbe
    {
        return FFProbe::create([
            'ffmpeg.binaries' => Config::get('services.ffmpeg.binaries'),
            'ffprobe.binaries' => Config::get('services.ffmpeg.ffprobe'),
        ]);
    }

    private static function formatDuration($seconds): string
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    public static function getMediaDetails($file)
    {
        if (!File::exists($file)) {
            return ['status' => false, 'error' => 'File not found.'];
        }

        $cacheKey = 'media_details_'.md5($file);
        return Cache::remember($cacheKey, 60, function () use ($file) {
            try {
                $ffprobe = self::ffprobe();
                $streams = $ffprobe->streams($file);

                $audioStream = $streams->audios()->first();
                $videoStream = $streams->videos()->first();


                if ($audioStream) {
                    return [
                        'status' => true,
                        'type' => ProductTypeEnum::SOUND->value,
                        'duration' => self::formatDuration($audioStream->get('duration')),
                        'details' => [
                            'bit_rate' => $audioStream->get('bit_rate'),
                            'sample_rate' => $audioStream->get('sample_rate'),
                            'channels' => $audioStream->get('channels'),
                            'duration' => $audioStream->get('duration'),
                            'format' => $ffprobe->format($file)->get('format_name')
                        ]
                    ];
                } elseif ($videoStream) {
                    return [
                        'status' => true,
                        'type' => ProductTypeEnum::VIDEO->value,
                        'duration' => self::formatDuration($videoStream->get('duration')),
                        'details' => [
                            'width' => $videoStream->get('width'),
                            'height' => $videoStream->get('height'),
                            'bit_rate' => $videoStream->get('bit_rate'),
                            'frame_rate' => $videoStream->get('r_frame_rate'),
                            'duration' => $videoStream->get('duration'),
                            'format' => $ffprobe->format($file)->get('format_name')
                        ]
                    ];
                } else {
                    return ['status' => false, 'error' => 'Unsupported media type'];
                }
            } catch (\Exception $e) {
                return ['status' => false, 'error' => $e->getMessage()];
            }
        });
    }


    /**
     * Trims a media file, either audio or video, based on specified start and end times.
     *
     * @param  string  $file  The path of the input file to be trimmed.
     * @param  string  $fileName  The name of the output file after trimming.
     * @param  int  $start  The starting time in seconds from where the trimming should begin.
     * @param  int  $end  The ending time in seconds where the trimming should stop.
     * @param  bool  $isAudio  Indicates whether the file is an audio (default is true). If false, it is assumed to be video.
     * @return array Status of the operation. Returns an array with 'status' key indicating success or failure,
     * and 'error' key containing the error message in case of failure.
     * @throws \Exception if an error occurs during the trimming process.
     */
    public static function trimMedia(string $file, string $fileName, int $start, int $end, bool $isAudio = true): array
    {
        Log::info('trim edilecek dosya: '.$file);
        try {
            if ($isAudio) {
                $ffmpeg = FFMpeg::create();
                $ffmpegFile = $ffmpeg->open($file);
                $from = TimeCode::fromSeconds($start);
                $to = TimeCode::fromSeconds($end);
                $ffmpegFile->filters()->clip($from, $to);
                $ffmpegFile->save(new Mp3(), $fileName);
            } else {
                $command = "ffmpeg -i $file -ss $start -to $end -c copy $fileName";
                exec($command, $output, $returnCode);
            }
            return ['status' => true];
        } catch (\Exception $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }
}