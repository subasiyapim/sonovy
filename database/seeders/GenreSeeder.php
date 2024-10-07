<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'Pop',
            'Rock',
            'Jazz',
            'Blues',
            'Country',
            'Rap',
            'Hip Hop',
            'R&B',
            'Soul',
            'Reggae',
            'Folk',
            'Classical',
            'Electronic',
            'Dance',
            'House',
            'Techno',
            'Trance',
            'Dubstep',
            'Drum and Bass',
            'Hardcore',
            'Garage',
            'Grime',
            'Indie',
            'Alternative',
            'Punk',
            'Metal',
            'Grunge',
            'Goth',
            'Emo',
            'Ska',
            'Funk',
            'Disco',
            'Gospel',
            'Choral',
            'Barbershop',
            'Opera',
            'Musical Theatre',
            'Film Scores',
            'Soundtracks',
            'World Music',
            'Latin',
            'Salsa',
            'Tango',
            'Flamenco',
            'Bossa Nova',
            'Samba',
            'African',
            'Asian',
            'Middle Eastern'];

        Genre::upsert(
            collect($genres)->map(function ($genre) {
                return [
                    'name' => $genre,
                    'code' => Str::slug($genre),
                ];
            })->toArray(),
            ['name'],
            ['code']
        );
    }
}
