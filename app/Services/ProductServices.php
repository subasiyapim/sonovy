<?php

namespace App\Services;

use App\Enums\ProductTypeEnum;
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


    public static function stepCompletedStatus(Product $product)
    {
        return [
            'common' => self::checkCommonFields($product),
            'step1' => self::checkStep1($product),
            'step2' => self::checkStep2($product),
            'step3' => self::checkStep3($product),
            'step4' => self::checkStep4($product),
        ];

    }

    private static function checkCommonFields(Product $product): bool
    {
        $commonFields = ['type', 'album_name', 'created_by', 'status'];

        foreach ($commonFields as $field) {
            if (empty($product->$field)) {
                return false;
            }
        }

        return true;
    }

    private static function checkStep1(Product $product): bool
    {
        $step1Fields = [
            ProductTypeEnum::SOUND->value => [
                'mixed_album',
                'genre_id',
                'sub_genre_id',
                'format_id',
                'label_id',
            ],
            ProductTypeEnum::VIDEO->value => [
                'video_type',
                'is_for_kids',
            ],
            ProductTypeEnum::RINGTONE->value => [
                'grid_code',
            ],
        ];

        $typeValue = $product->type->value ?? null;

        if (!isset($step1Fields[$typeValue])) {
            return false;
        }

        foreach ($step1Fields[$typeValue] as $field) {
            if (!isset($product->$field) || $product->$field === null) {
                return false;
            }
        }

        if (empty($product->mainArtists) || $product->mainArtists->isEmpty()) {
            return false;
        }

        return true;
    }

    private static function checkStep2(Product $product): bool
    {
        if (empty($product->songs)) {
            return false;
        }

        foreach ($product->songs as $song) {
            if ($song->is_completed != 1) {
                return false;
            }
        }

        return true;
    }

    private static function checkStep3(Product $product): bool
    {
        $step3Fields = [
            'production_year',
            'publishing_country_type',
            'physical_release_date',
        ];

        foreach ($step3Fields as $field) {
            if (!isset($product->$field) || $product->$field === null) {
                return false;
            }
        }

        if ($product->previously_released == 0 && empty($product->previous_release_date)) {
            return false;
        }

        return true;
    }

    private static function checkStep4(Product $product): bool
    {
        return true;
    }

}
