<?php

namespace App\Console\Commands;

use App\Models\Earning;
use App\Models\Platform;
use App\Models\Product;
use App\Models\System\Country;
use App\Models\System\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateDemoEarningsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:earnings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demo kazançları oluşturur';

    private const STREAMING_TYPES = ['Freemium', 'Premium', 'Ad-Supported', ''];
    private const PLATFORMS = ['Spotify', 'Apple Music', 'YouTube Music', 'Amazon Music', 'Facebook / Instagram', 'TikTok', 'Deezer'];
    private const SALES_TYPES = ['Stream', 'PLATFORM PROMOTION', 'Creation', 'Download'];
    private const RELEASE_TYPES = ['Music Release', 'Ringtone', 'Video', 'User Generated Content'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Demo kazançları oluşturuluyor...');

        // Tüm tenantları al
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            $this->info("\nTenant {$tenant->id} için işlem başlatılıyor...");
            
            try {
                tenancy()->initialize($tenant);
                $this->createEarningsForTenant();
            } catch (\Exception $e) {
                $this->error("Tenant {$tenant->id} için hata: " . $e->getMessage());
                Log::error("Tenant {$tenant->id} için demo kazanç oluşturma hatası", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                continue;
            }
        }

        $this->info("\nTüm tenantlar için işlem tamamlandı.");
    }

    private function createEarningsForTenant()
    {
        $faker = \Faker\Factory::create();
        $totalProcessed = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            // CSV dosyasını oku
            $csvPath = public_path('assets/sample-earnings-tr.csv');
            $csvFile = fopen($csvPath, 'r');
            
            // Header'ı atla
            fgetcsv($csvFile, 0, ';', '"', '\\');

            // Mevcut ürünleri ve şarkıları al
            $products = DB::connection('tenant')->table('products')
                ->join('product_song', 'products.id', '=', 'product_song.product_id')
                ->join('songs', 'product_song.song_id', '=', 'songs.id')
                ->join('artist_product', 'products.id', '=', 'artist_product.product_id')
                ->join('artists', 'artist_product.artist_id', '=', 'artists.id')
                ->join('labels', 'products.label_id', '=', 'labels.id')
                ->join('product_download_platform', 'products.id', '=', 'product_download_platform.product_id')
                ->select('products.*', 'songs.id as song_id', 'songs.isrc', 'artists.id as artist_id', 'labels.id as label_id')
                ->get();

            if ($products->isEmpty()) {
                throw new \Exception('Demo kazanç oluşturmak için uygun ürün bulunamadı.');
            }

            // CSV'den verileri oku
            while (($row = fgetcsv($csvFile, 0, ';')) !== false) {
                try {
                    $randomProduct = $products->random();
                    
                    $sales_date = Carbon::parse($faker->dateTimeBetween('-1 year', now()));
                    
                    $data = [
                        'user_id' => 1, // Tenant'taki admin kullanıcısı ID'si
                        'report_date' => Carbon::parse($faker->dateTimeBetween($sales_date, now()))->format('Y-m-d'),
                        'reporting_month' => $sales_date->format('Y/m/01'),
                        'sales_date' => $sales_date->format('Y-m-d'),
                        'sales_month' => $sales_date->format('Y/m/01'),
                        'platform' => $row[2] ?? '',
                        'platform_id' => DB::connection('tenant')->table('platforms')->where('name', $row[2])->first()?->id,
                        'country' => $row[3] ?? '',
                        'region' => $faker->randomElement(['Europe', 'North America', 'South America', 'Asia', 'Africa', 'Oceania']),
                        'country_id' => Country::where('name', $row[3])->first()?->id,
                        'label_name' => $row[4] ?? '',
                        'label_id' => $randomProduct->label_id,
                        'artist_name' => $row[5] ?? '',
                        'artist_id' => $randomProduct->artist_id,
                        'release_name' => $row[6] ?? '',
                        'song_name' => $row[7] ?? '',
                        'song_id' => $randomProduct->song_id,
                        'upc_code' => $randomProduct->upc_code,
                        'isrc_code' => $randomProduct->isrc,
                        'catalog_number' => $row[10] ?? '',
                        'streaming_type' => $row[11] ?? '',
                        'streaming_subscription_type' => $row[11] ?? '',
                        'release_type' => $row[12] ?? '',
                        'sales_type' => $row[13] ?? '',
                        'quantity' => intval($row[14] ?? 0),
                        'currency' => $row[15] ?? 'EUR',
                        'client_payment_currency' => $row[15] ?? 'EUR',
                        'unit_price' => str_replace(',', '.', $row[16] ?? 0),
                        'mechanical_fee' => str_replace(',', '.', $row[17] ?? 0),
                        'gross_revenue' => str_replace(',', '.', $row[18] ?? 0),
                        'client_share_rate' => str_replace(',', '.', $row[19] ?? 0),
                        'earning' => str_replace(',', '.', $row[20] ?? 0),
                    ];

                    DB::connection('tenant')->table('earnings')->insert($data);
                    $totalProcessed++;
                    $this->info("Kazanç oluşturuldu: {$data['platform']} - {$data['song_name']} - {$data['earning']} EUR");

                } catch (\Exception $e) {
                    $errors++;
                    $this->error("Hata: {$e->getMessage()}");
                    $this->error("Satır: " . json_encode($row));
                    continue;
                }
            }

            fclose($csvFile);

            DB::commit();

            $this->info('Demo kazanç verileri oluşturuldu');
            $this->info("Toplam işlenen: {$totalProcessed}");
            $this->info("Toplam hata: {$errors}");

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
