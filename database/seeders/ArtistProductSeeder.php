<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Product;
use App\Models\User;
use App\Services\EarningService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArtistProductSeeder extends Seeder
{
    public function run(): void
    {
        // Önce mevcut ilişkileri temizle
        DB::table('artist_product')->truncate();

        $products = Product::all();
        $artists = Artist::all();
        $users = User::whereHas('roles', function($query) {
            $query->where('code', 'label');
        })->get();

        // Önce ürün-sanatçı ilişkilerini kur
        foreach ($products as $product) {
            // Her ürüne 1-3 arası rastgele sanatçı ekle
            $randomArtists = $artists->random(rand(1, 3));
            $product->artists()->attach($randomArtists->pluck('id')->toArray());
        }

        // Şimdi her kullanıcı için demo kazanç oluştur
        foreach ($users as $user) {
            try {
                // Kullanıcı olarak giriş yap
                Auth::login($user);
                
                // Demo kazanç oluştur
                EarningService::createDemoEarnings();
                
                $this->command->info("Demo kazançlar oluşturuldu: {$user->email}");
            } catch (\Exception $e) {
                $this->command->error("Hata oluştu ({$user->email}): " . $e->getMessage());
            }
        }

        // Admin kullanıcısı ile tekrar giriş yap
        $admin = User::whereHas('roles', function($query) {
            $query->where('code', 'admin');
        })->first();
        
        if ($admin) {
            Auth::login($admin);
        }
    }
} 