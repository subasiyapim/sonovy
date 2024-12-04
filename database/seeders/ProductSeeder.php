<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Product;
use App\Models\Song;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $products = Product::factory(150)->create()->each(function (Product $row) {
            try {
                $imageUrl = 'https://picsum.photos/id/'.rand(1, 1000).'/500/500';

                if (!@getimagesize($imageUrl)) {
                    $imageUrl = 'https://picsum.photos/id/'.rand(1, 1000).'/500/500';
                }

                $row->addMediaFromUrl($imageUrl)
                    ->usingFileName(Str::slug($row->album_name).'.jpg') // Burada "name" yerine "album_name" kullanılabilir
                    ->usingName($row->album_name)
                    ->toMediaCollection('products', 'tenant_'.tenant('domain').'_products');

                $row->mainArtists()->attach(Artist::inRandomOrder()->first()->id, ['is_main' => 1]);
                $row->featuredArtists()->attach(Artist::inRandomOrder()->first()->id, ['is_main' => 0]);
                $row->songs()->attach(Song::inRandomOrder()->first()->id);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        });
    }
}
