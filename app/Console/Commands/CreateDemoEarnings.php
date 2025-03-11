<?php

namespace App\Console\Commands;

use App\Exports\FakerEarningReport;
use App\Models\Platform;
use App\Models\System\Tenant;
use App\Services\EarningService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CreateDemoEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:create-earnings
                            {user_id=1 : Demo kazançların atanacağı kullanıcı ID}
                            {platform_id? : Demo kazançların oluşturulacağı platform ID}
                            {--tenant= : Belirli bir tenant için çalıştır}
                            {--download : Oluşturulan dosyayı sisteme yüklemek yerine download et}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Belirtilen platform için demo kazanç raporu oluşturur ve opsiyonel olarak indirilmesini sağlar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Demo kazançlar oluşturma işlemi başlatılıyor...');

        try {
            $userId = $this->argument('user_id');
            $platformId = $this->argument('platform_id');
            $specificTenant = $this->option('tenant');
            $downloadOption = $this->option('download');

            // Platform kontrolü
            if ($platformId) {
                $platform = Platform::find($platformId);
                if (!$platform) {
                    $this->error("Platform ID: {$platformId} bulunamadı!");
                    return 1;
                }
                $this->info("Platform: {$platform->name} için işlem yapılacak.");
            }

            // Tenant'ları al
            $tenants = Tenant::query()
                ->when($specificTenant, function ($query) use ($specificTenant) {
                    $query->where('id', $specificTenant);
                })
                ->get();

            if ($tenants->isEmpty()) {
                $this->error('İşlem yapılacak tenant bulunamadı!');
                return 1;
            }

            $bar = $this->output->createProgressBar(count($tenants));
            $bar->start();

            $generatedData = [];

            foreach ($tenants as $tenant) {
                $this->info("\nTenant: " . ($tenant->name ?? "ID: {$tenant->id}") . " için işlem başlatılıyor...");

                try {
                    // Tenant'a geç
                    tenancy()->initialize($tenant);

                    // Demo verileri oluştur
                    if ($downloadOption) {
                        // Eğer download seçeneği aktifse, verileri topla ama sisteme kaydetme
                        $demoData = EarningService::createDemoEarningsData($userId, $platformId);
                        $generatedData = array_merge($generatedData, $demoData);
                    } else {
                        // Normal şekilde işlem yap ve sisteme kaydet
                        EarningService::createDemoEarnings($userId, $platformId);
                    }

                    $this->info("Tenant: " . ($tenant->name ?? "ID: {$tenant->id}") . " için demo kazançlar oluşturuldu.");

                } catch (\Exception $e) {
                    $this->error("Tenant: " . ($tenant->name ?? "ID: {$tenant->id}") . " için hata: " . $e->getMessage());
                    continue;
                } finally {
                    // Tenant bağlantısını temizle
                    tenancy()->end();
                }

                $bar->advance();
            }

            $bar->finish();

            // Download seçeneği aktifse Excel dosyasını oluştur
            if ($downloadOption && !empty($generatedData)) {
                $filename = 'demo_earnings_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

                // Sembolik link oluşturulmamışsa uyarı ver
                if (!file_exists(public_path('storage'))) {
                    $this->warn("\nDikkat: public/storage sembolik bağlantısı bulunamadı.");
                    $this->warn("Lütfen şu komutu çalıştırın: php artisan storage:link");
                }

                // Excel dosyasını public diskte oluştur
                Excel::store(new FakerEarningReport($generatedData), $filename, 'public');

                // Dosya yolunu ve indirme bağlantısını oluştur
                $filePath = storage_path('app/public/' . $filename);
                $downloadUrl = asset('storage/' . $filename);

                $this->info("\nDemo kazanç dosyası oluşturuldu: {$filePath}");
                $this->info("İndirmek için şu bağlantıyı kullanabilirsiniz: {$downloadUrl}");
            } else {
                $this->info("\nTüm tenantlar için demo kazanç oluşturma işlemi tamamlandı!");
            }

        } catch (\Exception $e) {
            $this->error('Genel bir hata oluştu: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
