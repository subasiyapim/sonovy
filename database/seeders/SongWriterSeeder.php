<?php

namespace Database\Seeders;

use App\Models\SongWriter;
use Illuminate\Database\Seeder;

class SongWriterSeeder extends Seeder
{
    public function run(): void
    {
        SongWriter::factory()
            ->count(20)
            ->create();
    }
} 