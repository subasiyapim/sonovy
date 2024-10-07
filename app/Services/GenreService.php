<?php

namespace App\Services;

use App\Http\Resources\Panel\CountryResource;
use App\Models\Country;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenreService
{
    public static function get()
    {
        return Genre::all();
    }

    public static function getActiveCountriesFromInputFormat(): array
    {
        $data = self::get();

        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'value' => $item['id'],
                'label' => $item['name'],
            ];
        }
        return $result;
    }

}
