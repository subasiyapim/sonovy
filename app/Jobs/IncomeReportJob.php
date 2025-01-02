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
use Maatwebsite\Excel\Facades\Excel;

class IncomeReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $start_date;
    public $end_date;
    public $user_id;
    public $report_type;
    public $label_ids;
    public $artist_ids;
    public $product_ids;
    public $song_ids;
    public $platform_ids;
    public $country_ids;
    protected $tenants;

    public function __construct($start_date = null, $end_date = null, $user_id = null, $report_type = '', $data = null)
    {
        $this->start_date = $start_date ?? Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
        $this->end_date = $end_date ?? Carbon::now()->endOfYear()->format('Y-m-d');
        $this->user_id = $user_id;
        $this->report_type = $report_type;

        if ($report_type && $report_type !== 'all' && $data) {
            $property = $report_type.'_ids';
            $this->$property = $data;
        }

        $this->tenants = Cache::rememberForever('tenants', function () {
            return \App\Models\System\Tenant::all();
        });
    }

    public function handle(): void
    {
        foreach ($this->tenants as $tenant) {

            tenancy()->initialize($tenant);

            if (!$this->start_date && !$this->end_date) {
                Log::warning('Start date and end date are required for generating reports.');
            } else {
                if ($this->report_type && $this->report_type !== 'all' && $this->{$this->report_type.'_ids'}) {
                    $this->generateReportsWithType();
                } else {
                    $this->generateReports();
                }
            }

        }

        Cache::forget('tenants');
    }

    protected function generateReportsWithType(): void
    {
        $userIds = $this->user_id ? [$this->user_id] : Earning::distinct()->pluck('user_id');

        foreach ($userIds as $userId) {
            $this->generateReport($this->start_date, $this->end_date, null, null, $userId,
                $this->{$this->report_type.'_ids'});
        }
    }

    protected function generateReports(): void
    {
        $userIds = $this->user_id ? [$this->user_id] : Earning::distinct()->pluck('label_id');

        foreach ($userIds as $userId) {
            $this->generateReport($this->start_date, $this->end_date, null, null, $userId);
        }
    }

    protected function generateReport(
        $start_date,
        $end_date,
        $quarterName = null,
        $year = null,
        $userId = null,
        $data = null
    ): void {
        $earnings = Earning::with('report.song.products', 'user')
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereHas('report', function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('sales_date', [$start_date, $end_date]);
                });
            })
            ->when($this->label_ids, function ($query) {
                $query->whereHas('report', function ($query) {
                    Log::info('Labels: '.json_encode($this->label_ids));
                    $query->whereIn('label_id', $this->label_ids);
                });
            })
            ->when($this->artist_ids, function ($query) {
                $query->whereHas('report.song.products.artists', function ($query) {
                    $query->whereIn('artist_id', $this->artist_ids);
                });
            })
            ->when($this->product_ids, function ($query) {
                $query->whereHas('report.song.products', function ($query) {
                    $query->whereIn('product_id', $this->product_ids);
                });
            })
            ->when($this->song_ids, function ($query) {
                $query->whereHas('report.song', function ($query) {
                    $query->whereIn('id', $this->song_ids);
                });
            })
            ->when($this->platform_ids, function ($query) {
                $query->whereHas('report.song.products.downloadPlatforms', function ($query) {
                    $query->whereIn('platform_id', $this->platform_ids);
                });
            })
            ->when($this->country_ids, function ($query) {
                $country = Country::find($this->country_ids);
                if ($country) {
                    $query->where('country', $country->name);
                }
            })
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();

        // Log the earnings to check if data is fetched
        Log::info('Earnings: '.$earnings->toJson());

        $monthlyAmounts = $earnings->groupBy(function ($earning) {
            return Carbon::parse($earning->report->sales_date)->format('m');
        })->map(function ($month) {
            return $month->sum('earning');
        });

        $logFileName = $quarterName && $year ? "{$quarterName}-{$year}" : Carbon::parse($start_date)->format('Y-m-d').'-'.Carbon::parse($end_date)->format('Y-m-d').'-'.$this->report_type;

        if ($earnings->isNotEmpty()) {
            if ($quarterName && $year) {
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
            } else {
                $report = Report::create(
                    [
                        'period' => $logFileName,
                        'user_id' => $userId,
                        'amount' => $earnings->sum('earning'),
                        'monthly_amount' => $monthlyAmounts,
                        'status' => 0,
                    ]
                );

                // Log the report to check if it includes monthly_amount
                Log::info('Report Created: '.$report->toJson());

                $reportExport = new ReportExport($earnings, $logFileName, $report);
                $reportExport->saveAndUpload();
            }
        }
    }

}
