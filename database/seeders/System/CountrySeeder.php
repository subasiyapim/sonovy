<?php

namespace Database\Seeders\System;

use App\Models\System\City;
use App\Models\System\Country;
use App\Models\System\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = public_path('assets/countries-states-cities.json');
        dump('Dosya boyutu: ' . round(filesize($filePath) / 1024 / 1024, 2) . ' MB');

        $data = File::get($filePath);
        dump('JSON verisi uzunluğu: ' . strlen($data));

        $countries = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            dump('JSON decode hatası: ' . json_last_error_msg());
            return;
        }

        if (!is_array($countries)) {
            dump('JSON verisi dizi değil!');
            dump($countries);
            return;
        }

        dump('Toplam ülke sayısı: ' . count($countries));

        // İlk ülkenin yapısını kontrol et
        if (count($countries) > 0) {
            dump('İlk ülke örneği:', array_keys($countries[0]));
        }

        foreach ($countries as $countryData) {
            try {
                // Ülke oluştur
                $country = Country::query()->firstOrCreate(
                    [
                        'name' => $countryData['name'],
                        'iso2' => $countryData['iso2'] ?? null,
                    ],
                    [
                        'iso3' => $countryData['iso3'] ?? null,
                        'numeric_code' => $countryData['numeric_code'] ?? null,
                        'phone_code' => $countryData['phonecode'] ?? null,
                        'capital' => $countryData['capital'] ?? null,
                        'currency' => $countryData['currency'] ?? null,
                        'currency_name' => $countryData['currency_name'] ?? null,
                        'currency_symbol' => $countryData['currency_symbol'] ?? null,
                        'tld' => $countryData['tld'] ?? null,
                        'native' => $countryData['native'] ?? null,
                        'region' => $countryData['region'] ?? null,
                        'subregion' => $countryData['subregion'] ?? null,
                        'nationality' => $countryData['nationality'] ?? null,
                        'timezones' => $countryData['timezones'] ?? [],
                        'translations' => $countryData['translations'] ?? [],
                        'latitude' => $countryData['latitude'] ?? null,
                        'longitude' => $countryData['longitude'] ?? null,
                        'emoji' => $countryData['emoji'] ?? null,
                        'emojiU' => $countryData['emojiU'] ?? null,
                    ]
                );

                // Şehirleri oluştur
                if (isset($countryData['states'])) {
                    foreach ($countryData['states'] as $cityData) {
                        $city = City::query()->firstOrCreate(
                            [
                                'country_id' => $country->id,
                                'name' => $cityData['name'],
                            ],
                            [
                                'state_code' => $cityData['state_code'] ?? null,
                                'type' => $cityData['type'] ?? null,
                                'latitude' => $cityData['latitude'] ?? null,
                                'longitude' => $cityData['longitude'] ?? null,
                            ]
                        );

                        // İlçeleri oluştur
                        if (isset($cityData['cities'])) {
                            foreach ($cityData['cities'] as $districtData) {
                                District::query()->firstOrCreate(
                                    [
                                        'country_id' => $country->id,
                                        'city_id' => $city->id,
                                        'name' => $districtData['name'],
                                    ],
                                    [
                                        'latitude' => $districtData['latitude'] ?? null,
                                        'longitude' => $districtData['longitude'] ?? null,
                                    ]
                                );
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                dump('Hata: ' . $e->getMessage());
                dump('Ülke verisi:', $countryData);
            }
        }

        dump('Eklenen ülke sayısı: ' . Country::count());
        dump('Eklenen şehir sayısı: ' . City::count());
        dump('Eklenen ilçe sayısı: ' . District::count());
    }
}
