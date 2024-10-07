<?php

namespace App\Services;

use App\Models\HelpCenterVideo;
use Illuminate\Support\Str;

class HelpCenterVideoServices
{

    public static function videoUpload($model, $video): void
    {

        $name = $model->title[LocaleService::getLocalizationList()[0]];
        $file_name = Str::slug($name);
        $collection = $model->getTable();

        MediaServices::upload($model, $video, $name, null, $collection);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function create(array $data): mixed
    {
        $video = HelpCenterVideo::create($data);
        if (isset($data['video'])) {
            HelpCenterVideoServices::videoUpload($video, $data['video']);
        }

        return $video;
    }

    public static function update(HelpCenterVideo $video, $request): void
    {
        $video->update($request);
    }

    /**
     * @param $search
     * @return mixed
     */
    public static function search($search): mixed
    {
        return HelpCenterVideo::where('title', 'like', '%' . $search . '%')->get();
    }
}
