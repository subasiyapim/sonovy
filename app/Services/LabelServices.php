<?php

namespace App\Services;

use App\Models\Label;
use App\Models\Role;
use Illuminate\Support\Str;

class LabelServices
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
        $data["added_by"] = auth()->id();
        $label = Label::create($data);

        if (isset($data['image'])) {
            LabelServices::imageUpload($label, $data['image']);
        }

        //$label->roles()->sync(Role::where('code', 'label')->first()->id);
        return $label;
    }

    public static function update(Label $label, $request): void
    {
        $label->update($request);
        if (isset($request['image'])) {
            LabelServices::imageUpload($label, $request['image']);
        }
    }

    /**
     * @param $search
     * @return mixed
     */
    public static function search($search): mixed
    {
        return Label::where('name', 'like', '%' . $search . '%')->get();
    }
}
