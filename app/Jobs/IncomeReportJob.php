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

class IncomeReportJob implements ShouldQueue
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
            ->whereBetween('sales_date', [$this->start_date, $this->end_date])
            ->where('user_id', $this->user_id);

        if ($this->report_type !== 'all') {
            switch ($this->report_type) {
                case 'artists':
                case 'songs':
                case 'labels':
                case 'platforms':
                    $type = Str::singular($this->report_type);
                    $query->whereIn("{$type}_id", $this->data);
                    break;
                case 'products':
                    $query->whereHas('product', function ($q) {
                        $q->whereIn('id', $this->data);
                    });
                    break;
                case 'countries':
                    $query->whereIn('country_id', $this->data);
                    break;
                default:
                    if (Str::startsWith($this->report_type, 'multiple_')) {
                        $type = Str::singular(str_replace('multiple_', '', $this->report_type));
                        $query->selectRaw("{$type}_id, SUM(earning) as total_earning")
                            ->groupBy("{$type}_id");
                    }
                    break;
            }
        }

        // Sorguyu logla
        Log::info('SQL Query: '.$query->toSql());
        Log::info('Bindings: '.json_encode($query->getBindings()));

        $this->period = $this->generatePeriodName();
        $this->earnings = $query->get();

        if ($this->earnings->isEmpty()) {
            Log::info('Kazanç bulunamadı.');
            return;
        }

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
            'artists' => 'Artist',
            'songs' => 'Song',
            'platforms' => 'Platform',
            'products' => 'Product',
            'countries' => 'Country',
            'labels' => 'Label',
            'all' => 'Tüm',
            'multiple_artists' => 'Birden Fazla Artist',
            'multiple_songs' => 'Birden Fazla Song',
            'multiple_platforms' => 'Birden Fazla Platform',
            'multiple_products' => 'Birden Fazla Product',
            'multiple_countries' => 'Birden Fazla Country',
            'multiple_labels' => 'Birden Fazla Label',
        ];

        $type = $typeMap[$this->report_type] ?? 'Unknown';
        return $this->start_date.' - '.$this->end_date.' '.$type.' Raporu';
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
