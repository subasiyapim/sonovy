<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Song;
use App\Models\Platform;
use App\Models\SongWriter;
use App\Models\SongComposer;
use App\Models\SongMusician;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Enums\ProductTypeEnum;
use App\Enums\SongTypeEnum;
use Illuminate\Support\Facades\Http;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diskName = 'tenant_'.tenant('domain').'_products';

        Product::factory()
            ->count(50)
            ->create()
            ->each(function ($product) use ($diskName) {
                try {
                    // Picsum.photos servisini kullan
                    $imageUrl = 'https://picsum.photos/500/500?random=' . $product->id;
                    
                    $product->addMediaFromUrl($imageUrl)
                        ->usingFileName(Str::slug($product->album_name).'.jpg')
                        ->usingName($product->album_name)
                        ->toMediaCollection('products', $diskName);

                    // Ürün tipine göre şarkı tipi belirleme
                    $songType = match($product->type) {
                        ProductTypeEnum::SOUND => SongTypeEnum::SOUND,
                        ProductTypeEnum::VIDEO => SongTypeEnum::VIDEO,
                        ProductTypeEnum::RINGTONE => SongTypeEnum::RINGTONE,
                        ProductTypeEnum::APPLE_VIDEO => SongTypeEnum::VIDEO,
                    };

                    // 1-4 arası rastgele sayıda şarkı oluştur
                    $songs = Song::factory(rand(1, 4))->create([
                        'type' => $songType,
                        'created_by' => $product->created_by,
                        'genre_id' => $product->genre_id,
                        'sub_genre_id' => $product->sub_genre_id,
                    ]);

                    // Şarkıları ürüne bağla
                    if ($songs->isNotEmpty()) {
                        $product->songs()->attach($songs->pluck('id')->toArray());

                        // Her şarkı için besteci, söz yazarı ve müzisyen ekle
                        foreach ($songs as $song) {
                            // 1-3 arası söz yazarı ekle
                            SongWriter::factory(rand(1, 3))->create([
                                'song_id' => $song->id,
                            ]);

                            // 1-3 arası besteci ekle
                            SongComposer::factory(rand(1, 3))->create([
                                'song_id' => $song->id,
                            ]);

                            // 1-5 arası müzisyen ekle
                            SongMusician::factory(rand(1, 5))->create([
                                'song_id' => $song->id,
                            ]);
                        }
                    }

                    // Rastgele 2-5 platform seç ve ürüne bağla
                    $platforms = Platform::inRandomOrder()->take(rand(2, 5))->get();
                    foreach ($platforms as $platform) {
                        $product->downloadPlatforms()->attach($platform->id, [
                            'price' => array_rand(Platform::$PLATFORM_DOWNLOAD_PRICE),
                            'pre_order_date' => now()->addDays(rand(1, 30)),
                            'publish_date' => now()->addDays(rand(31, 60)),
                            'status' => rand(0, 1),
                            'time' => now()->format('H:i:s'),
                            'date' => now()->format('Y-m-d'),
                            'hashtags' => json_encode($this->faker->words(rand(3, 6))),
                            'description' => $this->faker->sentence(),
                        ]);

                        // History tablosuna da ekle
                        $product->histories()->attach($platform->id, [
                            'status' => rand(0, 1),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            });
    }
}
