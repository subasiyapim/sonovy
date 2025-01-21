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
        $artist = new Artist();
        $artist->fill([
            'name' => 'Various Artists',
            'country_id' => Country::where('iso2', 'TR')->first()->id,
            'created_by' => 1,
        ])->save();
        $diskName = 'tenant_'.tenant('domain').'_artists';

        Artist::factory(10)
            ->create()
            ->each(function (Artist $artist) use ($diskName) {
                try {
                    $randomArtistBranchIds = [];
                    $usedIds = [];

                    for ($i = 0; $i < rand(1, 6); $i++) {
                        $artistBranch = (new ArtistBranch())->newQuery()->inRandomOrder()->whereNotIn('id', $usedIds)->first();
                        if ($artistBranch) {
                            $randomArtistBranchIds[] = $artistBranch->id;
                            $usedIds[] = $artistBranch->id;
                        }
                    }

                    $artist->artistBranches()->attach($randomArtistBranchIds);

                    // Picsum.photos servisini kullan
                    $imageUrl = 'https://dummyimage.com/500x500/3498db/ffffff&text=' . urlencode($artist->name);

                    $artist->addMediaFromUrl($imageUrl)
                        ->usingFileName(Str::slug($artist->name).'.jpg')
                        ->usingName($artist->name)
                        ->toMediaCollection('artists', $diskName);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            });
    }
}
