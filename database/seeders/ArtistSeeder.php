<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\ArtistBranch;
use App\Models\System\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArtistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artist::create([
            'name' => 'Various Artists',
            'country_id' => Country::where('iso2', 'TR')->first()->id,
            'created_by' => 1,
        ]);
        $diskName = 'tenant_'.tenant('domain').'_artists';

        Artist::factory(26)
            ->create()
            ->each(function (Artist $artist) use ($diskName) {
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

                $imageUrl = 'https://picsum.photos/id/'.rand(1, 1000).'/500/500';

                if (!@getimagesize($imageUrl)) {
                    $imageUrl = 'https://picsum.photos/500/500';
                }

                $artist->addMediaFromUrl($imageUrl)
                    ->usingFileName(Str::slug($artist->name).'.jpg')
                    ->usingName($artist->name)
                    ->toMediaCollection('artists', $diskName);
            });
    }
}
