<?php

namespace Database\Seeders;

use App\Models\ArtistBranch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArtistBranchSeeder extends Seeder
{
    protected static array $seeders = [
        [
            [
                'en' => [
                    'name' => 'Musician',
                ],
                'tr' => ['name' => 'Müzisyen',]
            ],
            [
                'en' => [
                    'name' => 'Singer',
                ],
                'tr' => ['name' => 'Şarkıcı',]
            ],
            [
                'en' => [
                    'name' => 'Composer',
                ],
                'tr' => ['name' => 'Besteci',]
            ],
            [
                'en' => [
                    'name' => 'Lyricist',
                ],
                'tr' => ['name' => 'Söz Yazarı',]
            ],
            [
                'en' => [
                    'name' => 'Conductor',
                ],
                'tr' => ['name' => 'Şef',]
            ],
            [
                'en' => [
                    'name' => 'Arranger',
                ],
                'tr' => ['name' => 'Aranjör',]
            ],
            [
                'en' => [
                    'name' => 'Producer',
                ],
                'tr' => ['name' => 'Yapımcı',]
            ],
            [
                'en' => [
                    'name' => 'DJ',
                ],
                'tr' => ['name' => 'DJ',]
            ],
            [
                'en' => [
                    'name' => 'Sound Engineer',
                ],
                'tr' => ['name' => 'Ses Mühendisi',]
            ],
            [
                'en' => [
                    'name' => 'Music Director',
                ],
                'tr' => ['name' => 'Müzik Direktörü',]
            ],
            [
                'en' => [
                    'name' => 'Music Teacher',
                ],
                'tr' => ['name' => 'Müzik Öğretmeni',]
            ],
            [
                'en' => [
                    'name' => 'Music Therapist',
                ],
                'tr' => ['name' => 'Müzik Terapisti',]
            ],
            [
                'en' => [
                    'name' => 'Musicologist',
                ],
                'tr' => ['name' => 'Müzikolog',]
            ],
            [
                'en' => [
                    'name' => 'Music Critic',
                ],
                'tr' => ['name' => 'Müzik Eleştirmeni']
            ]


        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::$seeders as $seeder) {
            foreach ($seeder as $data) {
                ArtistBranch::create($data);
            }
        }
    }
}
