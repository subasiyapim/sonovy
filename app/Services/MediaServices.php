<?php

namespace App\Services;

use App\Models\Song;
use Illuminate\Support\Facades\Log;


class MediaServices
{
    /**
     * @param $model
     * @param $media
     * @param  string  $collection_name
     * @param  string  $disk
     * @return void
     */

    public static function upload($model, $media, string $collection_name = 'default', string $disk = 'public'): void
    {
        if (!$media) {
            return;
        }

        Log::info('MediaServices upload media',
            ['media' => $media, 'disk' => $disk, 'collection_name' => $collection_name]);


        $name = uniqid().'-'.time();
        $file_name = uniqid().'-'.time().'-'.$media->getClientOriginalName();

        $diskName = 'tenant_'.tenant('domain').'_'.$disk;

        $model->addMedia($media)
            ->usingFileName($file_name)
            ->usingName($name)
            ->toMediaCollection($collection_name, $diskName);
    }

    public static function mediaUpload(mixed $media, $type = 1)
    {
        return Song::create(
            [
                'name' => $media->getClientOriginalName(),
                'type' => $type,
                'path' => $media->store('songs', 'public'),
                'mime_type' => $media->getMimeType(),
                'size' => $media->getSize(),
                'created_by' => auth()->id
            ]
        );
    }


}
