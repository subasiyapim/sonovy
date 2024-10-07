<?php

namespace App\Services;

use App\Models\SiteFeature;
use Illuminate\Support\Str;

class SiteFeatureServices
{
    public static function imageUpload($model, $image): void
    {
        $name = $model->name;
        $file_name = Str::slug($name);
        $collection = $model->getTable();

        MediaServices::upload($model, $image, $name, $file_name, $collection, $collection);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function create(array $data): mixed
    {
        $site_feature = SiteFeature::create($data);

        if (isset($data['image'])) {
            SiteFeatureServices::imageUpload($site_feature, $data['image']);
        }

        return $site_feature;
    }

    public static function update(SiteFeature $site_feature, $request): void
    {
        $site_feature->update($request);
        if (isset($request['image'])) {
            SiteFeatureServices::imageUpload($site_feature, $request['image']);
        }
    }

    /**
     * @param $search
     * @return mixed
     */
    public static function search($search): mixed
    {
        return SiteFeature::where('name', 'like', '%' . $search . '%')->get();
    }
}
