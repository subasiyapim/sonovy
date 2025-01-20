<?php

namespace Database\Seeders;

use App\Models\SongComposer;
use Illuminate\Database\Seeder;

class SongComposerSeeder extends Seeder
{
    public function run(): void
    {
        SongComposer::factory()
            ->count(20)
            ->create();
    }
} 