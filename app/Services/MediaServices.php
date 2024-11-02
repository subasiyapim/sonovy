<?php

namespace App\Services;

use App\Models\Song;
use Illuminate\Support\Facades\Storage;

class MediaServices
{
    /**
     * @param $model
     * @param $media
     * @param  string  $name
     * @param  string  $file_name
     * @param  string  $collection_name
     * @return void
     */

    public static function upload($model, $media, string $collection_name = 'default', string $disk = 'public'): void
    {
        if (!$media) {
            return;
        }

        $name = uniqid().'-'.time();
        $file_name = uniqid().'-'.time().'-'.$media->getClientOriginalName();
        $disk = 'tenant_'.tenant('id');

        $model->addMedia($media)
            ->usingFileName($file_name)
            ->usingName($name)
            ->toMediaCollection($collection_name, $disk);
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
