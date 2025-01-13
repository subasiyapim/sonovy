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
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class RequestedIncomeReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $start_date;
    public $end_date;
    public $user_id;
    public $report_type;
    public $data;
    public $earnings;
    protected $period;
    protected $name;
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
        $this->start_date = \Carbon\Carbon::parse($this->start_date);
        $this->end_date = \Carbon\Carbon::parse($this->end_date);

        $this->earnings = Earning::with('report.song.products', 'user')
            ->where('user_id', $this->user_id)
            ->whereBetween('report_date', [$this->start_date, $this->end_date])
            ->get();

        $this->name = $this->generateName();
        $this->period = $this->start_date->format('m-Y').' '.$this->end_date->format('m-Y');

        $parentReport = Report::create([
            'period' => $this->period,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'amount' => $this->earnings->sum('earning'),
            'monthly_amount' => $this->calculateMonthlyAmount($this->earnings),
            'status' => 1,
            'parent_id' => null,
        ]);

        $groupedEarnings = match ($this->report_type) {
            'multiple_artists' => $this->earnings->groupBy('artist_id'),
            'multiple_songs' => $this->earnings->groupBy('song_id'),
            'multiple_platforms' => $this->earnings->groupBy('platform'),
            'multiple_products' => $this->earnings->groupBy('release_name'),
            'multiple_countries' => $this->earnings->groupBy('country'),
            'multiple_labels' => $this->earnings->groupBy('label_id'),
            default => collect(),
        };

        if ($groupedEarnings->isEmpty()) {
            Log::error('Geçerli bir grup rapor verisi bulunamadı.');
            return;
        }

        foreach ($groupedEarnings as $groupKey => $earnings) {
            $groupName = match ($this->report_type) {
                'multiple_artists' => $earnings->first()?->artist?->name,
                'multiple_songs' => $earnings->first()?->song?->name,
                'multiple_platforms' => $earnings->first()?->platform,
                'multiple_products' => $earnings->first()?->release_name,
                'multiple_countries' => $earnings->first()?->country,
                'multiple_labels' => $earnings->first()?->label?->name,
                default => 'Unknown Group',
            } ?? 'Unknown Group';

            $filePath = $this->generateFilePath($parentReport->id);

            $this->generateReport($filePath, $groupName, $earnings, $parentReport->id);
        }
    }


    protected function generateReport($filePath, $groupName, $earnings, $parentId = null): void
    {
        $report = Report::create([
            'period' => $this->period,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'amount' => $earnings->sum('earning'),
            'monthly_amount' => $this->calculateMonthlyAmount($earnings),
            'file_path' => $filePath,
            'status' => 1,
            'parent_id' => $parentId,
        ]);

        $disk = 'tenant_'.tenant('domain').'_income_reports';

        $reportExport = new ReportExport($earnings, $this->period, $report);
        $fileName = $filePath.'/'.Str::slug($groupName).'.xlsx';
        Excel::store($reportExport, $fileName, $disk);
    }


    protected function calculateMonthlyAmount($earnings): float
    {
        $months = $this->start_date->diffInMonths($this->end_date) + 1;
        return $earnings->sum('earning') / max($months, 1);
    }

    protected function generateFilePath($parent_id): string
    {
        return 'multiple_reports/'.$this->user_id.'/'.Str::slug($this->period).'/'.$parent_id.'/';
    }

    protected function generateName(): string
    {
        $typeMap = [
            'multiple_artists' => 'Sanatçılar hakkında çoklu rapor',
            'multiple_songs' => 'Şarkılar hakkında çoklu rapor',
            'multiple_platforms' => 'Platformlar hakkında çoklu rapor',
            'multiple_products' => 'Ürünler hakkında çoklu rapor',
            'multiple_countries' => 'Ülkeler hakkında çoklu rapor',
            'multiple_labels' => 'Labeller hakkında çoklu rapor',
        ];

        return $typeMap[$this->report_type] ?? 'Unknown';
    }
}
