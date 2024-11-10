<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\ArtistBranch;
use App\Models\System\Country;
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('artists')->truncate();
        DB::table('artist_artist_branch')->truncate();

        Artist::create([
            'name' => 'Various Artists',
            'country_id' => Country::where('iso2', 'TR')->first()->id,
            'created_by' => 1,
        ]);

        Artist::factory(26)->create([
            'created_by' => \App\Models\User::inRandomOrder()->first()->id,
        ])->each(function (Artist $artist) {
            $randomArtistBranchIds = [];
            $usedIds = [];

            for ($i = 0; $i < rand(1, 6); $i++) {
                $artistBranch = ArtistBranch::inRandomOrder()->whereNotIn('id', $usedIds)->first();
                if ($artistBranch) {
                    $randomArtistBranchIds[] = $artistBranch->id;
                    $usedIds[] = $artistBranch->id;
                }
            }

            $artist->artistBranches()->attach($randomArtistBranchIds);

            $artist->addMediaFromUrl('https://picsum.photos/400/400')
                ->usingFileName(Str::slug($artist->name) . '.jpg');
            // ->toMediaCollection('artists', 'tenant_'.tenant('id'));
        });
    }
}
