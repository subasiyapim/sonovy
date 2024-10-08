<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\ArtistBranch;
use App\Models\System\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArtistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $randomArtistBranchIds = [];
        for ($i = 0; $i < 5; $i++) {
            $randomArtistBranchIds[] = ArtistBranch::inRandomOrder()->first()->id;
        }

        shuffle($randomArtistBranchIds);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('artists')->truncate();
        DB::table('artist_artist_branch')->truncate();


        Artist::factory(10)->create([
            'added_by' => \App\Models\User::inRandomOrder()->first()->id,
        ])->each(function (Artist $artist) use ($randomArtistBranchIds) {
            $artist->artistBranches()->attach($randomArtistBranchIds);

            $artist->addMediaFromUrl('https://picsum.photos/400/400')
                ->usingFileName(Str::slug($artist->name).'.jpg')
                ->toMediaCollection('artists', 'tenant_'.tenant('id'));
        });

    }
}
