<?php

namespace App\Jobs;

use App\Exports\ReportExport;
use App\Models\Product;
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
use Illuminate\Support\Facades\Schema;

ini_set('memory_limit', '512M');

class QuartersIncomeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $disk;
    const QUARTERS = [
        ['Q1', 1, 3],
        ['Q2', 4, 6],
        ['Q3', 7, 9],
        ['Q4', 10, 12],
    ];

    public $users;
    protected $tenants;

    public function __construct()
    {
        $this->tenants = Cache::rememberForever('tenants', function () {
            return \App\Models\System\Tenant::all();
        });

    }

    public function handle(): void
    {
        foreach ($this->tenants as $tenant) {

            tenancy()->initialize($tenant);
            $this->disk = 'tenant_'.$tenant->domain.'_income_reports';
            $this->users = User::with('earnings')
                ->whereHas('earnings')
                ->get();

            foreach ($this->users as $user) {
                $this->generateQuarterlyReports($user);
            }

        }

        Cache::forget('tenants');
    }

    protected function generateQuarterlyReports($user): void
    {
        $firstProduct = $user->products()->orderBy('created_at')->first();
        if (!$firstProduct) {
            Log::warning("User {$user->id} has no products.");
            return;
        }

        $firstProductYear = $firstProduct->created_at->year;
        $currentYear = Carbon::now()->year;
        $currentDate = Carbon::now();

        foreach (range($firstProductYear, $currentYear) as $year) {
            foreach (self::QUARTERS as [$quarterName, $startMonth, $endMonth]) {
                $start = Carbon::create($year, $startMonth, 1);
                $end = Carbon::create($year, $endMonth, 1)->endOfMonth();

                if ($year == $currentYear && ($start->greaterThan($currentDate) || $end->greaterThan($currentDate))) {
                    continue;
                }

                $userId = $user->id;
                $this->generateReport($start, $end, $quarterName, $year, $userId);
            }
        }
    }

    protected function generateReport($start_date, $end_date, $quarterName, $year, $userId): void
    {
        $earnings = Earning::with('report.song.products', 'user')
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereHas('report', function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('sales_date', [$start_date, $end_date]);
                });
            })
            ->where('user_id', $userId)
            ->get();

        $logFileName = "{$quarterName}-{$year}";

        if ($earnings->isNotEmpty()) {
            $monthlyAmounts = $earnings->groupBy(function ($earning) {
                return Carbon::parse($earning->report->sales_date)->format('m');
            })->map(function ($month) {
                return $month->sum('earning');
            });

            $report = Report::firstOrCreate(
                [
                    'period' => $logFileName,
                    'user_id' => $userId,
                ],
                [
                    'amount' => $earnings->sum('earning'),
                    'monthly_amount' => $monthlyAmounts,
                    'status' => 0,
                ]
            );

            if ($report->wasRecentlyCreated) {
                $reportExport = new ReportExport($earnings, $logFileName, $report);
                $reportExport->saveAndUpload($this->disk);
            }
        }
    }
}
