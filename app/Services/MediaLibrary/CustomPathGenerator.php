<?php

namespace App\Services\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{

    public function getPath(Media $media): string
    {
        return $media->model_id.'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $media->model_id.'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $media->model_id.'/responsive-images/';
    }
}
