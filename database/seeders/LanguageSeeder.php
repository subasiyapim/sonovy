<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $language_file = File::get(public_path('assets/countries.json'));
        $languages = json_decode($language_file, true);

        foreach ($languages as $language) {

            Language::firstOrCreate(
                [
                    'name' => $language['name'],
                    'iso2' => $language['iso2'],
                ],
                [
                    'iso3' => $language['iso3'],
                    'numeric_code' => $language['numeric_code'],
                    'phone_code' => $language['phone_code'],
                    'native' => $language['native'],
                    'nationality' => $language['nationality'],
                    'timezones' => $language['timezones'] ?? [],
                    'translations' => $language['translations'] ?? [],
                    'latitude' => $language['latitude'],
                    'longitude' => $language['longitude'],
                    'emoji' => $language['emoji'],
                    'emojiU' => $language['emojiU'],
                    'is_active' => true
                ]);
        }
    }
}
