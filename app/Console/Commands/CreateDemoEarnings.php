<?php

namespace App\Console\Commands;

use App\Models\System\Tenant;
use App\Services\EarningService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDemoEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:create-earnings {user_id=1 : Demo kazançların atanacağı kullanıcı ID} {--tenant= : Belirli bir tenant için çalıştır}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Her tenant için son 48 ay için demo kazanç kayıtları oluşturur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Demo kazançlar oluşturma işlemi başlatılıyor...');

        try {
            $userId = $this->argument('user_id');
            $specificTenant = $this->option('tenant');

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

            foreach ($tenants as $tenant) {
                $this->info("\nTenant: {$tenant->name} için işlem başlatılıyor...");

                try {
                    // Tenant'a geç
                    tenancy()->initialize($tenant);

                    // Demo verileri oluştur
                    EarningService::createDemoEarnings($userId);

                    $this->info("Tenant: {$tenant->name} için demo kazançlar oluşturuldu.");

                } catch (\Exception $e) {
                    $this->error("Tenant: {$tenant->name} için hata: " . $e->getMessage());
                    continue;
                } finally {
                    // Tenant bağlantısını temizle
                    tenancy()->end();
                }

                $bar->advance();
            }

            $bar->finish();
            $this->info("\nTüm tenantlar için demo kazanç oluşturma işlemi tamamlandı!");

        } catch (\Exception $e) {
            $this->error('Genel bir hata oluştu: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
