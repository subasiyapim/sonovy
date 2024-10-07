<?php

namespace App\Services;

use App\Models\Partner;
use Illuminate\Support\Str;

class PartnerServices
{
    public static function imageUpload($model, $image): void
    {
        $name = $model->name;
        $file_name = Str::slug($name);
        $collection = $model->getTable();

        MediaServices::upload($model, $image, $name, $file_name, $collection);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function create(array $data): mixed
    {
        $feature = Partner::create($data);

        if (isset($data['logo'])) {
            PartnerServices::imageUpload($feature, $data['logo']);
        }

        return $feature;
    }

    public static function update(Partner $feature, $request): void
    {
        $feature->update($request);
        if (isset($request['logo'])) {
            PartnerServices::imageUpload($feature, $request['logo']);
        }
    }

    /**
     * @param $search
     * @return mixed
     */
    public static function search($search): mixed
    {
        return Partner::where('name', 'like', '%' . $search . '%')->get();
    }
}
