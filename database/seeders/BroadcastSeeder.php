<?php

namespace Database\Seeders;

use App\Models\Broadcast;
use App\Models\Hashtag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BroadcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Broadcast::factory(10)->create()->each(function (Broadcast $broadcast) {
            $broadcast->addMediaFromUrl('https://picsum.photos/400/400')
                ->usingFileName(Str::random(10) . '.jpg')
                ->toMediaCollection('broadcasts');

            for ($i = 0; $i < 5; $i++) {
                $hashtag = Str::random(10);
                $code = Str::slug($hashtag);

                $broadcast->hashtags()->create([
                    'model_type' => Broadcast::class,
                    'model_id' => $broadcast->id,
                    'name' => $hashtag . ' ' . $i,
                    'code' => $code . '-' . $i,
                ]);

            }
        });
    }
}
