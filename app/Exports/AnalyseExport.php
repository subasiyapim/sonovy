<?php

namespace App\Exports;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Maatwebsite\Excel\Concerns\FromCollection;

class AnalyseExport implements FromCollection
{
    protected $earnings;
    protected $slug;

    public function __construct($earnings, $slug)
    {
        $this->earnings = $earnings;
        $this->slug = $slug;
    }

    /**
     * @return Factory|View|Application
     */
    public function collection(): View|Factory|Application
    {
        return view('excel.exports.analyses.earning_from_sales_type', [
            'earnings' => $this->earnings
        ]);
    }
}
