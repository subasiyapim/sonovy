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


    public static function stepCompletedStatus(Product $product)
    {
        $stepFields = [
            'common' => [
                'type',
                'album_name',
                'created_by',
                'status',
            ],
            'step1' => [
                ProductTypeEnum::SOUND->value => [
                    'mixed_album',
                    'genre_id',
                    'sub_genre_id',
                    'format_id',
                    'label_id',
                    'language_id',
                ],
                ProductTypeEnum::VIDEO->value => [
                    'video_type',
                    'is_for_kids',
                ],
                ProductTypeEnum::RINGTONE->value => [
                    'grid_code',
                ],
            ],
            'step2' => [
            ],
            'step3' => [
                'production_year',
                'previously_released',
                'previous_release_date',
                'publishing_country_type',
                'physical_release_date',
            ],
            'step4' => [],
        ];

        $completedSteps = [];

        foreach ($stepFields as $step => $fields) {
            if ($step === 'step1') {
                $fields = $fields[$product->type->value] ?? [];
            }

            $allFieldsFilled = true;

            foreach ($fields as $field) {
                if (in_array($field, $stepFields['common']) && empty($product->$field)) {
                    $allFieldsFilled = false;
                    break;
                }

                if (!in_array($field, $stepFields['common']) && empty($product->$field)) {
                    $allFieldsFilled = false;
                    break;
                }

                if ($step === 'step3' && $field === 'previously_released' && $product->previously_released) {
                    if (empty($product->physical_release_date)) {
                        $allFieldsFilled = false;
                        break;
                    }
                }

                if ($step === 'step2') {
                    $allSongCompleted = false;
                }

                foreach ($product->songs as $song) {
                    if ($song->is_completed == 1) {
                        $allSongCompleted = true;
                    } else {
                        $allSongCompleted = false;
                        break;
                    }
                }

                if ($step === 'step2' && !$allSongCompleted) {
                    $allFieldsFilled = false;
                    break;
                }
            }

            $completedSteps[$step] = $allFieldsFilled;
        }
        
        return $completedSteps;
    }

    public static function search($search): mixed
    {
        return Product::with('songs')->where('name', 'like', '%'.$search.'%')->get();
    }
}
