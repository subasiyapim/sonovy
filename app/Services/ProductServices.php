<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Participant;
use App\Models\Song;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductServices
{
    public static function imageUpload($model, $image): void
    {
        $name = Str::random(10).'-'.time();
        $file_name = Str::slug($name);
        $collection = 'products';

        MediaServices::upload($model, $image, $name, $file_name, $collection, $collection);
    }

    public static function progress(Product $product)
    {
        $fields = array_diff($product->getFillable(), Product::$excludedFields);

        $totalFields = count($fields);

        $filledFields = 0;

        foreach ($fields as $field) {
            if (!empty($product->$field)) {
                $filledFields++;
            }
        }

        return round(($filledFields / $totalFields) * 100);

    }

    public static function search($search): mixed
    {
        return Product::with('songs')->where('name', 'like', '%'.$search.'%')->get();
    }
}
