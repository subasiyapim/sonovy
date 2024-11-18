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
        return Artist::with('platforms')
            ->whereNot('name', 'Various Artists')
            ->where('name', 'like', '%' . $search . '%')
            ->get();
    }

    /**
     * @param $artists
     * @return array
     * her bir sanatçının yayınlarda kullandığı şarkı türleri döner. Tekrar eden türleri çıkartır.
     */
    public static function usedGenres($artists): array
    {
        return $artists->flatMap(function ($artist) {
            return $artist->products->map(function ($product) {
                return [
                    'id' => $product?->genre?->id,
                    'name' => $product?->genre?->name,
                ];
            });
        })->unique('id')->values()->all();
    }
}
