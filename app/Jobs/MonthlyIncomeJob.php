<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Earning;
use App\Models\Report;
use App\Exports\ReportExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\TenantAwareCommand;
use Stancl\Tenancy\Database\TenantCollection;
use App\Models\System\Tenant;

class MonthlyIncomeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const MONTHS_IN_YEAR = 12;

    private $tenants;
    private $storageDisk;

    public function __construct()
    {
        // Tenantları önbellekten alıyoruz
        $this->tenants = Tenant::all()->toArray();
    }

    /**
     * Görevi çalıştıran ana handle metodu.
     */
    public function handle(): void
    {
        foreach ($this->tenants as $tenant) {
            // Tenant başlatılıyor
            $this->initializeTenant($tenant);

            // Kullanıcılara ait kazanç raporlarını işliyoruz
            $usersWithEarnings = $this->getUsersWithEarnings();
            foreach ($usersWithEarnings as $user) {
                $this->processUserReports($user);
            }
        }

        // Süreç sonunda önbellekteki tenant bilgilerini temizle
        Cache::forget('tenants');
    }

    /**
     * Tenant'ı başlat ve depolama diskini ayarla.
     */
    private function initializeTenant(array $tenant): void
    {
        tenancy()->initialize($tenant); // Tenant başlatılıyor
        Log::info('Loaded tenants:', $this->tenants);

        $this->storageDisk = "tenant_{$tenant['domain']}_income_reports";
    }

    /**
     * Kazancı olan tüm kullanıcıları getirir.
     */
    private function getUsersWithEarnings()
    {
        $users = User::with('earnings')
            ->whereHas('earnings') // Kazancı olan kullanıcıları sorgula
            ->get();


        Log::info('Users with earnings:', $users->pluck('id')->toArray());

        return $users;
    }

    /**
     * Her kullanıcı için raporları hazırlar.
     */
    private function processUserReports(User $user): void
    {
        $firstProductDate = $this->getFirstProductDate($user);

        if (!$firstProductDate) {
            Log::warning("User {$user->id} has no products.");
            return;
        }

        $dateRanges = $this->calculateDateRanges($firstProductDate->year, Carbon::now());

        foreach ($dateRanges as $dateRange) {
            if (is_array($dateRange) && count($dateRange) === 4) {
                // Raporu üretmek için tarih aralığını kullan
                $this->generateReport(
                    $dateRange[0], // Başlangıç tarihi
                    $dateRange[1], // Bitiş tarihi
                    $dateRange[2], // Yıl
                    $dateRange[3], // Ay
                    $user->id
                );
            } else {
                Log::error('Hatalı dateRange formatı bulundu:', ['dateRange' => $dateRange]);
            }
        }
    }

    /**
     * Kullanıcının ürünleri arasında ilk oluşturulma tarihini bulur.
     */
    private function getFirstProductDate(User $user): ?Carbon
    {
        $firstProduct = $user->products()->orderBy('created_at')->first();
        return $firstProduct?->created_at;
    }

    /**
     * Belirtilen yıl ve ay aralığında tarih grupları oluşturur.
     */
    private function calculateDateRanges(int $startYear, Carbon $currentDate): array
    {
        $result = [];
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;

        foreach (range($startYear, $currentYear) as $year) {
            foreach (range(1, self::MONTHS_IN_YEAR) as $month) {
                if ($year === $currentYear && $month >= $currentMonth) {
                    continue; // Gelecekteki ayları dahil etmeyin
                }

                $start = Carbon::create($year, $month, 1);
                $end = $start->copy()->endOfMonth();
                $result[] = [$start, $end, $year, $month];
            }
        }

        return $result;
    }

    /**
     * Belirtilen tarih aralığı için rapor oluşturur.
     */
    private function generateReport(Carbon $start, Carbon $end, int $year, int $month, int $userId): void
    {
        $earningsByPeriod = Earning::with('report')
            ->whereBetween('sales_date', [$start, $end])
            ->where('user_id', $userId)
            ->get()
            ->groupBy(fn($earning) => $earning->sales_date->format('Y-m'));

        foreach ($earningsByPeriod as $period => $earnings) {
            $this->processEarningsForPeriod($period, $earnings, $userId);
        }
    }

    /**
     * Kazanç verilerini işleme alır.
     */
    private function processEarningsForPeriod(string $period, $earnings, int $userId): void
    {
        if ($earnings->isEmpty()) {
            return;
        }

        $totalEarning = $earnings->sum('earning');
        $report = $this->createOrUpdateReport($period, $userId, $totalEarning);

        if ($report->wasRecentlyCreated) {
            $this->handleMissingEarningsReports($earnings);
            (new ReportExport($earnings, $period, $report))->saveAndUpload($this->storageDisk);
        }
    }

    /**
     * Mevcut raporu oluşturur veya günceller.
     */
    private function createOrUpdateReport(string $period, int $userId, float $totalEarning)
    {
        return Report::firstOrCreate(
            ['period' => $period, 'user_id' => $userId],
            ['amount' => $totalEarning, 'status' => 0, 'is_auto_report' => true]
        );
    }

    /**
     * Eksik kazanç raporlarını kontrol eder ve loglar.
     */
    private function handleMissingEarningsReports($earnings): void
    {
        foreach ($earnings as $earning) {
            if (!$earning->report) {
                Log::warning("Missing report for Earning ID: {$earning->id}");
            }
        }
    }
}