<?php

namespace App\Exports;

use App\Models\Report;
use App\Services\MediaServices;
use Illuminate\Http\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class ReportExport implements FromCollection, WithHeadings
{
    public $earnings;
    public $period;
    public Report $report;

    public function __construct($earnings, $period, $report)
    {
        $this->earnings = $earnings;
        $this->period = $period;
        $this->report = $report;
    }

    /**
     * @return Collection
     */
    public function collection()
    {

        $data = [];
        $count = 0;
        foreach ($this->earnings as $earning) {
            $data[] = [
                '#' => $count++,
                'name' => $earning->report->name,
                'report_date' => $earning->report_date,
                'sales_date' => $earning->sales_date,
                'platform' => $earning->platform,
                'country' => $earning->country,
                'label_name' => $earning->report->label_name,
                'artist_name' => $earning->report->artist_name,
                'release_name' => $earning->report->release_name,
                'song_name' => $earning->report->song_name,
                'upc_code' => $earning->report->upc_code,
                'isrc_code' => $earning->report->isrc_code,
                'catalog_number' => $earning->report->catalog_number,
                'release_type' => $earning->report->release_type,
                'sales_type' => $earning->report->sales_type,
                'quantity' => $earning->quantity,
                'currency' => $earning->currency,
                'net_revenue' => $earning->earning,
            ];
        }
        $this->report->update([
            'status' => 1
        ]);

        return collect($data);

    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Report Date',
            'Sales Date',
            'Platform',
            'Country',
            'Label Name',
            'Artist Name',
            'Release Name',
            'Song Name',
            'UPC Code',
            'ISRC Code',
            'Catalog Number',
            'Release Type',
            'Sales Type',
            'Quantity',
            'Currency',
            'Net Revenue',
        ];
    }

    public function saveAndUpload($disk = 'public')
    {
        $filePath = $this->period.'.xlsx';
        Excel::store(new self($this->earnings, $this->period, $this->report), $filePath, $disk);

        $storedFile = Storage::disk($disk)->path($filePath);

        $this->report->addMedia(new File($storedFile))
            ->usingFileName($filePath)
            ->toMediaCollection($disk, $disk);
    }

}
