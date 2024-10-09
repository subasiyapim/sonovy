<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ConvertAudio;
use App\Models\Setting;
use App\Models\Song;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Audio\Mp3;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Cache;


class FFMpegServices
{
    public static function trimAudio($file, $file_name, $start, $end)
    {
        //$command = "ffmpeg -i $file -ss $start -to $end -c copy $file_name";
        try {

            //return exec($command, $output, $returnCode);
            $ffmpeg = FFMpeg::create();
            $ffmpeg_file = $ffmpeg->open($file);
            $from = TimeCode::fromSeconds($start);
            $to = TimeCode::fromSeconds($end);
            $ffmpeg_file->filters()->clip($from, $to);
            $ffmpeg_file->save(new Mp3(), $file_name);

            return ['status' => true];
        } catch (\Exception $e) {

            return ['status' => false, 'error' => $e->getMessage()];
        }

    }

    public static function trimVideo($file, $file_name, $start, $end)
    {

        $command = "ffmpeg -i $file -ss $start -to $end -c copy $file_name";
        try {

            exec($command, $output, $returnCode);
            /*$ffmpeg = FFMpeg::create();
            $ffmpeg_file = $ffmpeg->open($file);
            $from = TimeCode::fromSeconds($start);
            $to = TimeCode::fromSeconds($end);
            $ffmpeg_file->filters()->clip($from, $to);
            $ffmpeg_file->save(new X264(), 'file.mp4');*/

            return ['status' => true];
        } catch (\Exception $e) {

            return ['status' => false, 'error' => $e->getMessage()];
        }
    }

    public static function getAudioDetails($file)
    {

        try {

            $ffprobe = FFProbe::create();
            $stream = $ffprobe->streams($file)->audios();

            $audio = $stream->first();

            return [
                'status' => true,
                [
                    'bit_rate' => $audio->get('bit_rate'),
                    'sample_rate' => $audio->get('sample_rate'),
                    'channels' => $audio->get('channels'),
                    'duration' => $ffprobe->format($file)->get('duration'),
                    'format' => $ffprobe->format($file)->get('format_name')
                ]
            ];

        } catch (\Exception $e) {

            return ['status' => false, 'error' => $e->getMessage()];
        }

    }

    public static function getVideoDetails($file)
    {

        try {

            $ffprobe = FFProbe::create();

            $stream = $ffprobe->streams($file)->videos();

            $video = $stream->first();

            return [
                'status' => true,
                [
                    'width' => $video->get('width'),
                    'height' => $video->get('height'),
                    'bit_rate' => $video->get('bit_rate'),
                    'frame_rate' => $video->get('r_frame_rate'),
                    'duration' => $ffprobe->format($file)->get('duration'),
                    'format' => $ffprobe->format($file)->get('format_name')

                ]
            ];

        } catch (\Exception $e) {

            return ['status' => false, 'error' => $e->getMessage()];
        }

    }

}
