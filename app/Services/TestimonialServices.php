<?php

namespace App\Services;

use App\Models\Testimonial;
use Illuminate\Support\Str;

class TestimonialServices
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
        $testimonial = Testimonial::create($data);

        if (isset($data['avatar'])) {
            TestimonialServices::imageUpload($testimonial, $data['avatar']);
        }

        return $testimonial;
    }

    public static function update(Testimonial $testimonial, $request): void
    {
        $testimonial->update($request);
        if (isset($request['avatar'])) {
            TestimonialServices::imageUpload($testimonial, $request['avatar']);
        }
    }

    /**
     * @param $search
     * @return mixed
     */
    public static function search($search): mixed
    {
        return Testimonial::where('name', 'like', '%' . $search . '%')->get();
    }
}
