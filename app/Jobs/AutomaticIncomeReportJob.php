<?php

namespace App\Jobs;

use App\Events\ReportProcessed;
use App\Exports\ReportExport;
use App\Models\Earning;
use App\Models\Report;
use App\Models\System\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class AutomaticIncomeReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $start_date;
    public $end_date;
    public $user_id;
    public $report_type;
    public $ids;
    public $earnings;
    protected $period;
    protected $name;
    protected $monthly_amount;
    protected $report;

    public function __construct($start_date, $end_date, $user_id, $report_type, $ids)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->user_id = $user_id;
        $this->report_type = $report_type;
        $this->ids = $ids;
    }

    public function handle(): void
    {
        $query = Earning::with('report.song.products', 'user')
            ->whereBetween('report_date', [$this->start_date, $this->end_date])
            ->where('user_id', $this->user_id);

        if ($this->report_type !== 'all' && !empty($this->ids)) {
            $query->where(function ($q) {
                match ($this->report_type) {
                    'artists' => $q->whereIn('artist_id', $this->ids),
                    'songs' => $q->whereIn('song_id', $this->ids),
                    'platforms' => $q->whereIn('platform_id', $this->ids),
                    'products' => $q->whereHas('product', function ($query) {
                        $query->whereIn('id', $this->ids);
                    }),
                    'countries' => $q->whereIn('country_id', $this->ids),
                    'labels' => $q->whereIn('label_id', $this->ids),
                    default => null
                };
            });
        }

        $this->name = $this->generateName();
        $this->period = Carbon::parse($this->start_date)->format('m-Y').' '.Carbon::parse($this->end_date)->format('m-Y');
        $this->earnings = $query->get();

        $this->monthly_amount = $this->earnings->groupBy(function ($earning) {
            return Carbon::parse($earning->sales_date)->format('m');
        })->map(function ($monthEarnings) {
            return $monthEarnings->sum('earning');
        })->toArray();

        $this->generateReport();
    }

    protected function generateName(): string
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

        return $typeMap[$this->report_type] ?? 'Unknown';
    }

    protected function generateReport(): void
    {
        $this->report = Report::create([
            'period' => $this->period,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'amount' => $this->earnings->sum('earning'),
            'monthly_amount' => $this->monthly_amount,
            'status' => 1,
            'report_type' => $this->report_type,
            'report_ids' => $this->ids
        ]);

        // Rapor dışa aktarma ve kaydetme
        $disk = 'tenant_'.tenant('domain').'_income_reports';
        $reportExport = new ReportExport($this->earnings, $this->period, $this->report);
        $reportExport->saveAndUpload($disk);

        tenancy()->runForMultiple(Tenant::all(), function ($tenant) {
            broadcast(event: new ReportProcessed(['process' => 'success', 'report_id' => $this->report],
                $tenant->id, $this->user_id));
        });
    }
}
