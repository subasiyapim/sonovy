<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait MediaTrait
{
    /**
     * @param        $model
     * @param        $media
     * @param bool   $name
     * @param string $collection_name
     * @param string $disk
     * @return void
     */
    public function mediaUpload(
        $model,
        $media,
        bool $name = false,
        string $collection_name = 'default',
        string $disk = 'public'
    ) : void {
        if (!$media) {
            return;
        }

        $name = $name
            ? $name . '.' . pathinfo($media->getClientOriginalName(), PATHINFO_EXTENSION)
            : $media->getClientOriginalName();

        $model->addMedia($media)
            ->setFileName($name ?? $media->getClientOriginalName())
            ->toMediaCollection($collection_name, $disk);
    }

}
