<?php

namespace App\Services;

use App\Enums\ProductTypeEnum;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

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
                $duration = $ffprobe->format($file)->get('duration');
                $formattedDuration = self::formatDuration($duration);

                if ($audioStream) {
                    return [
                        'status' => true,
                        'type' => ProductTypeEnum::SOUND->value,
                        'details' => [
                            'bit_rate' => $audioStream->get('bit_rate'),
                            'sample_rate' => $audioStream->get('sample_rate'),
                            'channels' => $audioStream->get('channels'),
                            'duration' => $formattedDuration,
                            'format' => $ffprobe->format($file)->get('format_name')
                        ]
                    ];
                } elseif ($videoStream) {
                    return [
                        'status' => true,
                        'type' => ProductTypeEnum::VIDEO->value,
                        'details' => [
                            'width' => $videoStream->get('width'),
                            'height' => $videoStream->get('height'),
                            'bit_rate' => $videoStream->get('bit_rate'),
                            'frame_rate' => $videoStream->get('r_frame_rate'),
                            'duration' => $formattedDuration,
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
}