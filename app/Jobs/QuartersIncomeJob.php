<?php

namespace App\Jobs;

use App\Exports\ReportExport;
use App\Models\Broadcast;
use App\Models\Earning;
use App\Models\Report;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class QuartersIncomeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const QUARTERS = [
        ['Q1', 1, 3],
        ['Q2', 4, 6],
        ['Q3', 7, 9],
        ['Q4', 10, 12],
    ];

    public $users;

    public function __construct()
    {
        $this->users = User::with('earnings')
            ->whereHas('earnings')
            ->get();
    }

    public function handle(): void
    {
        foreach ($this->users as $user) {
            $this->generateQuarterlyReports($user);
        }
    }

    protected function generateQuarterlyReports($user): void
    {
        $firstBroadcast = $user->broadcasts()->orderBy('created_at')->first();
        if (!$firstBroadcast) {
            Log::warning("User {$user->id} has no broadcasts.");
            return;
        }

        $firstBroadcastYear = $firstBroadcast->created_at->year;
        $currentYear = Carbon::now()->year;
        $currentDate = Carbon::now();

        foreach (range($firstBroadcastYear, $currentYear) as $year) {
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
        $earnings = Earning::with('report.song.broadcasts', 'user')
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
                $reportExport->saveAndUpload();
            }
        }
    }
}
