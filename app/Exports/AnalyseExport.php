<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AnalyseExport implements FromView
{
    protected $earnings;
    protected $slug;

    public function __construct($earnings, $slug)
    {
        $this->earnings = $earnings;
        $this->slug = $slug;
    }

    public function view(): View
    {
        return view('excel.exports.analyses.'.$this->slug, [
            'earnings' => $this->earnings,
        ]);
    }
}