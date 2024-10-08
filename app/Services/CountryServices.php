<?php

namespace App\Services;

use App\Http\Resources\Panel\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CountryServices
{
    public static function search($search): mixed
    {
        $search = Str::lower($search);

        return CountryResource::collection(Country::whereAny(
            ['name', 'iso2', 'phone_code', 'native'],
            'LIKE',
            "%$search%"
        )
            ->get())->resolve();
    }

    public static function update(Request $request, Country $country)
    {
        $request->validate([
            'name' => 'required|string',
            'iso2' => 'required|string',
            'iso3' => 'required|string',
            'numeric_code' => 'required|string',
            'phone_code' => 'required|string',
            'capital' => 'required|string',
            'currency' => 'required|string',
            'currency_name' => 'required|string',
            'currency_symbol' => 'required|string',
            'tld' => 'required|string',
            'native' => 'required|string',
            'region' => 'required|string',
            'region_id' => 'required|string',
            'subregion' => 'required|string',
            'subregion_id' => 'required|string',
            'nationality' => 'required|string',
            'timezones' => 'required|array',
            'translations' => 'required|array',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'emoji' => 'required|string',
            'emojiU' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $country->update($request->all());

    }

    public static function get()
    {
        return CountryResource::collection(\App\Models\System\Country::get())->resolve();
    }

    public static function getActiveCountriesFromInputFormat()
    {
        $countries = self::get();

        $result = [];
        foreach ($countries as $country) {
            $result[] = [
                'value' => $country['id'],
                'label' => $country['name'],
                'iconKey' => $country['emoji']
            ];
        }
        return $result;
    }

}
