<?php

namespace App\Jobs;

use App\Exports\ReportExport;
use App\Models\Country;
use App\Models\Earning;
use App\Models\Platform;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

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
        // Temel sorgu oluşturma
        $query = Earning::with('report.song.products', 'user')
            ->whereBetween('sales_date', [$this->start_date, $this->end_date])
            ->where('user_id', $this->user_id);

        // Rapor türüne göre filtreleme
        if ($this->report_type !== 'all') {
            switch ($this->report_type) {
                case 'artists':
                case 'songs':
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
                        $query->groupBy("{$type}_id");
                    }
                    break;
            }
        }

        // Rapor başlığını belirleme
        $this->period = $this->generatePeriodName();

        // Kazançları alma
        $this->earnings = $query->get();

        // Eğer kazançlar boşsa işlemi sonlandır
        if ($this->earnings->isEmpty()) {
            Log::info('Kazanç bulunamadı.');
            return;
        }

        // Aylık miktarları hesaplama
        $this->monthly_amount = $this->earnings->groupBy(function ($earning) {
            return Carbon::parse($earning->sales_date)->format('m');
        })->map(function ($monthEarnings) {
            return $monthEarnings->sum('earning');
        })->toArray();

        // Rapor oluşturma
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
            'all' => 'Tüm'
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
        $reportExport = new ReportExport($this->earnings, 'lofilename', $report);
        $reportExport->saveAndUpload();
    }
}
