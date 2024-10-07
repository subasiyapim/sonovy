<?php

namespace App\Services;

use App\Models\Artist;
use Illuminate\Support\Str;

class ArtistServices
{
    public static function imageUpload($model, $image): void
    {
        $name = $model->name;
        $file_name = Str::slug($name);
        $collection = $model->getTable();

        MediaServices::upload($model, $image, $name, $file_name, $collection, $collection);
    }

    public static function search($search): mixed
    {
        return Artist::with('platforms')->where('name', 'like', '%' . $search . '%')->get();
    }
}
