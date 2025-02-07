<?php

namespace App\Jobs;

use App\Exports\ReportExport;
use App\Models\Earning;
use App\Models\Report;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\TenantAwareCommand;

class QuartersIncomeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use TenantAwareCommand;
    use HasATenantsOption;

    const MEMORY_LIMIT = '512M';
    const QUARTERS = [
        ['name' => 'Q1', 'startMonth' => 1, 'endMonth' => 3],
        ['name' => 'Q2', 'startMonth' => 4, 'endMonth' => 6],
        ['name' => 'Q3', 'startMonth' => 7, 'endMonth' => 9],
        ['name' => 'Q4', 'startMonth' => 10, 'endMonth' => 12],
    ];

    private $disk;
    private $tenants;

    public function __construct()
    {
        ini_set('memory_limit', self::MEMORY_LIMIT);
        $this->tenants = $this->getTenants();
    }

    protected function getTenants()
    {
        return Cache::rememberForever('tenants', function () {
            return \App\Models\System\Tenant::all();
        });
    }

    public function handle(): void
    {
        foreach ($this->tenants as $tenant) {
            tenancy()->initialize($tenant);
            $this->disk = 'tenant_'.$tenant->domain.'_income_reports';

            // Kullanıcı rapor oluşturma işlemi soyutlanmış fonksiyona yollandı
            $this->generateReportsForUsers();
        }
    }

    protected function generateReportsForUsers(): void
    {
        $users = User::with('earnings')
            ->whereHas('earnings')
            ->get();

        foreach ($users as $user) {
            $this->generateQuarterlyReports($user);
        }
    }

    protected function generateQuarterlyReports($user): void
    {
        // İlk kazanç bilgisi alınır
        $firstEarning = Earning::where('user_id', $user->id)
            ->orderBy('sales_date')
            ->first();

        if (!$firstEarning) {
            Log::warning("User {$user->id} has no earnings.");
            return;
        }

        // İlk kazancın bulunduğu çeyrek
        $firstQuarter = $this->getQuarterFromDate(Carbon::parse($firstEarning->sales_date));
        $firstYear = Carbon::parse($firstEarning->sales_date)->year;
        $currentYear = now()->year;
        $currentDate = now();

        foreach (range($firstYear, $currentYear) as $year) {
            $quarters = ($year === $firstYear)
                ? $this->getQuartersFromFirstQuarter($firstQuarter) // İlk yıl için özel çeyrekler
                : self::QUARTERS; // Diğer yıllar için tüm çeyrekler

            foreach ($quarters as $quarter) {
                [$start, $end] = $this->getStartAndEndDates($year, $quarter);

                // Çeyrek henüz tamamlanmamışsa atla
                if ($end->greaterThan($currentDate)) {
                    Log::info("Skipping future or incomplete quarter: {$quarter['name']}-{$year}");
                    continue;
                }

                // Daha önce rapor oluşturulmuşsa tekrar oluşturma
                $existingReport = Report::where('period', "{$quarter['name']}-{$year}")
                    ->where('user_id', $user->id)
                    ->exists();

                if ($existingReport) {
                    Log::info("Report already exists for User {$user->id}, Period: {$quarter['name']}-{$year}");
                    continue;
                }

                // Yeni rapor oluştur
                $this->generateReport($start, $end, $quarter['name'], $year, $user->id);
                //Log::info("Report generated for User {$user->id}, Period: {$quarter['name']}-{$year}");
            }
        }
    }

    protected function getQuarterFromDate(Carbon $date): array
    {
        $month = $date->month;

        if ($month >= 1 && $month <= 3) {
            return self::QUARTERS[0];
        } elseif ($month >= 4 && $month <= 6) {
            return self::QUARTERS[1];
        } elseif ($month >= 7 && $month <= 9) {
            return self::QUARTERS[2];
        } else {
            return self::QUARTERS[3];
        }
    }

    protected function getQuartersFromFirstQuarter(array $firstQuarter): array
    {
        $startQuarterIndex = array_search($firstQuarter['name'], array_column(self::QUARTERS, 'name'));

        return array_slice(self::QUARTERS, $startQuarterIndex);
    }

    protected function getStartAndEndDates($year, $quarter): array
    {
        $start = Carbon::create($year, $quarter['startMonth'], 1)->startOfDay();
        $end = Carbon::create($year, $quarter['endMonth'], 1)->endOfMonth()->endOfDay();

        return [$start, $end];
    }

    protected function generateReport($startDate, $endDate, $quarterName, $year, $userId): void
    {
        $earnings = Earning::whereBetween('sales_date', [$startDate, $endDate])
            ->where('user_id', $userId)
            ->get();

        if ($earnings->isEmpty()) {
            return;
        }

        $logFileName = "{$quarterName}-{$year}";
        $monthlyAmounts = $earnings->groupBy(fn($earning) => Carbon::parse($earning->sales_date)->format('m'))
            ->map(fn($month) => $month->sum('earning'));

        $report = Report::firstOrCreate(
            ['period' => $logFileName, 'user_id' => $userId],
            [
                'amount' => $earnings->sum('earning'),
                'monthly_amount' => $monthlyAmounts,
                'status' => 0,
                'is_auto_report' => true,
            ]
        );

        if ($report->wasRecentlyCreated) {
            $reportExport = new ReportExport($earnings, $logFileName, $report);
            $reportExport->saveAndUpload($this->disk);
        }
    }
}