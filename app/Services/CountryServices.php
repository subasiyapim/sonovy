<?php

namespace App\Services;

use App\Enums\ProductPublishedCountryTypeEnum;
use App\Http\Resources\Panel\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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


    public static function getCountryPhoneCodes()
    {
        $countries = self::get();

        $result = [];
        foreach ($countries as $country) {
            $result[] = [
                'value' => $country['phone_code'],
                'label' => $country['name'],
                'iconKey' => $country['emoji']
            ];
        }
        return $result;
    }

    public static function getCountriesGroupedByRegion(): array
    {
        $countries = self::get();
        $result = [];
        foreach ($countries as $country) {
            $result[$country['region'] == "" ? 'Diğer' : $country['region']][] = [
                'value' => $country['id'],
                'label' => $country['name'],
                'iconKey' => $country['emoji']
            ];
        }
        return $result;
    }

    public static function getSelectedCountries($productId, $countryType): array
    {
        $groupedCountries = self::getCountriesGroupedByRegion();
        $selectedCountryIds = self::extractSelectedCountryIds($productId, $countryType);

        return self::markCountriesAsSelected($groupedCountries, $selectedCountryIds);
    }

    private static function extractSelectedCountryIds($productId, $countryType): array
    {
        return $countryType === ProductPublishedCountryTypeEnum::ALL->value
            ? self::get()
            : DB::table('product_published_country')
                ->where('product_id', $productId)
                ->get()
                ->pluck('country_id')
                ->toArray();
    }

    private static function markCountriesAsSelected(&$groupedCountries, $selectedCountryIds): array
    {
        foreach ($groupedCountries as $region => &$countries) {
            foreach ($countries as &$country) {
                $country['selected'] = in_array($country['value'], $selectedCountryIds);
            }
        }

        return $groupedCountries;
    }
}
