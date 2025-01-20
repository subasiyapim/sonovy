<?php

namespace Database\Seeders;

use App\Models\SongMusician;
use Illuminate\Database\Seeder;

class SongMusicianSeeder extends Seeder
{
    public function run(): void
    {
        SongMusician::factory()
            ->count(20)
            ->create();
    }
} 