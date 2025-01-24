<?php

namespace App\Jobs;

use App\Services\EarningService;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateDemoEarningsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 saat
    public $tries = 3; // 3 deneme hakkı
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($userId = null)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Demo kazançları oluşturuluyor...');
            $adminUser = (new User())->newQuery()->where('email', 'admin@admin.com')->first();
            if (!$adminUser) {
                throw new \Exception('Admin kullanıcısı bulunamadı');
            }
            EarningService::createDemoEarnings($adminUser->id);
            Log::info('Demo kazançları başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            Log::error('Demo kazançları oluşturulurken hata oluştu: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Demo kazançları oluşturma işi başarısız oldu: ' . $exception->getMessage());
    }
}
