<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Report;
use App\Models\Earning;
use App\Exports\ReportExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class MonthlyIncomeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tenants;
    public $disk;

    public function __construct()
    {
        $this->tenants = Cache::rememberForever('tenants', function () {
            return \App\Models\System\Tenant::all();
        });
    }

    public function handle(): void
    {
        foreach ($this->tenants as $tenant) {
            // Her tenant için veritabanını başlatıyoruz
            tenancy()->initialize($tenant);
            $this->disk = 'tenant_'.$tenant->domain.'_income_reports';

            // Kullanıcılara erişim
            $users = User::with('earnings')
                ->whereHas('earnings') // Kazancı olan kullanıcılar
                ->get();

            foreach ($users as $user) {
                $this->generateMonthlyReports($user);
            }
        }

        Cache::forget('tenants');
    }

    protected function generateMonthlyReports($user): void
    {
        $firstProduct = $user->products()->orderBy('created_at')->first();

        if (!$firstProduct) {
            Log::warning("User {$user->id} has no products.");
            return; // Ürünü yoksa işleme alma
        }

        $firstProductYear = $firstProduct->created_at->year;
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        foreach (range($firstProductYear, $currentYear) as $year) {
            foreach (range(1, 12) as $month) {
                if ($year == $currentYear && $month >= $currentMonth) {
                    continue; // Geçerli ve ilerideki ayları dahil etme
                }

                $start = Carbon::create($year, $month, 1);
                $end = $start->copy()->endOfMonth();

                $this->generateReport($start, $end, $year, $month, $user->id);
            }
        }
    }

    protected function generateReport($start, $end, $year, $month, $userId): void
    {
        $earnings = Earning::with('report') // 'report' ilişkisinin yüklendiğinden emin olun
        ->whereBetween('sales_date', [$start, $end])
            ->where('user_id', $userId)
            ->get()
            ->groupBy(function ($earning) {
                return $earning->sales_date->format('Y-m'); // Gruplama yap
            });

        if ($earnings->isEmpty()) {
            return; // Eğer hiçbir kazanç yoksa erken çık
        }

        foreach ($earnings as $period => $group) {
            $totalEarning = $group->sum('earning'); // Toplam kazanç
            $report = Report::firstOrCreate(
                [
                    'period' => $period,
                    'user_id' => $userId,
                ],
                [
                    'amount' => $totalEarning,
                    'status' => 0,
                    'is_auto_report' => true,
                ]
            );

            if ($report->wasRecentlyCreated) {
                // Eksik raporları kontrol et
                foreach ($group as $earning) {
                    if (!$earning->report) {
                        Log::warning("Missing report for Earning ID: {$earning->id}");
                        continue;
                    }

                    $reportDate = $earning->report->report_date ?? 'Undefined'; // report_date kontrolü
                    // İşlemler buradan devam eder...
                }

                $reportExport = new ReportExport($group, $period, $report);
                $reportExport->saveAndUpload($this->disk);
            }
        }
    }
}