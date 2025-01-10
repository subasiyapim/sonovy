<?php

namespace App\Jobs;

use App\Exports\ReportExport;
use App\Models\Earning;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AutomaticIncomeReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $start_date;
    public $end_date;
    public $user_id;
    public $report_type;
    public $data;
    public $earnings;
    protected $period;
    protected $monthly_amount;

    public function __construct($start_date, $end_date, $user_id, $report_type, $data)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->user_id = $user_id;
        $this->report_type = $report_type;
        $this->data = $data;
    }

    public function handle(): void
    {
        $query = Earning::with('report.song.products', 'user')
            ->whereBetween('report_date', [$this->start_date, $this->end_date])
            ->where('user_id', $this->user_id);


        $this->period = $this->generatePeriodName();
        $this->earnings = $query->get();

        $this->monthly_amount = $this->earnings->groupBy(function ($earning) {
            return Carbon::parse($earning->sales_date)->format('m');
        })->map(function ($monthEarnings) {
            return $monthEarnings->sum('earning');
        })->toArray();

        $this->generateReport();
    }

    protected function generatePeriodName(): string
    {
        $typeMap = [
            'artists' => 'Sanatçılar hakkında tek rapor',
            'songs' => 'Şarkılar hakkında tek rapor',
            'platforms' => 'Platformlar hakkında tek rapor',
            'products' => 'Albümler hakkında tek rapor',
            'countries' => 'Ülkeler hakkında tek rapor',
            'labels' => 'Labeller hakkında tek rapor',
            'all' => 'Tam katalog hakkında tek rapor',
        ];

        $type = $typeMap[$this->report_type] ?? 'Unknown';
        return $this->start_date.' - '.$this->end_date.' '.$type;
    }

    protected function generateReport(): void
    {
        $report = Report::create([
            'period' => $this->period,
            'user_id' => $this->user_id,
            'amount' => $this->earnings->sum('earning'),
            'monthly_amount' => $this->monthly_amount,
            'status' => 1,
        ]);

        // Rapor dışa aktarma ve kaydetme
        $disk = 'tenant_'.tenant('domain').'_income_reports';
        $reportExport = new ReportExport($this->earnings, $this->period, $report);
        $reportExport->saveAndUpload($disk);
    }
}
