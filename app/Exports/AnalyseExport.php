<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Log;

class AnalyseExport implements FromView
{
    protected $earnings;
    protected $slug;

    public function __construct($earnings, $slug)
    {
        $this->earnings = collect($earnings);
        $this->slug = $slug;
        Log::info('AnalyseExport constructed with ' . $this->earnings->count() . ' earnings and slug: ' . $this->slug);
    }

    public function view(): View
    {
        Log::info('View method called with earnings count: ' . $this->earnings->count());
        Log::info('View path: excel.exports.analyses.' . $this->slug);
        Log::info('Sample data:', ['data' => $this->earnings->first()]);

        $earnings = $this->earnings->map(function ($earning) {
            return (object) [
                'start_date' => $earning['start_date'] ?? '',
                'end_date' => $earning['end_date'] ?? '',
                'platform' => $earning['platform'] ?? '',
                'country' => $earning['country'] ?? '',
                'quantity' => $earning['quantity'] ?? 0,
                'earning' => $earning['earning'] ?? 0,
                'percentage' => $earning['percentage'] ?? 0,
                'label_name' => $earning['label_name'] ?? '',
                'song_name' => $earning['song_name'] ?? '',
                'artist_name' => $earning['artist_name'] ?? '',
                'album_name' => $earning['album_name'] ?? $earning['release_name'] ?? '',
                'streams' => $earning['streams'] ?? $earning['quantity'] ?? 0,
                'isrc_code' => $earning['isrc_code'] ?? $earning['isrc'] ?? '',
                'upc_code' => $earning['upc_code'] ?? ''
            ];
        });

        return view('excel.exports.analyses.' . $this->slug, [
            'earnings' => $earnings
        ]);
    }
}