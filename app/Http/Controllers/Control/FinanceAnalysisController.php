<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Resources\Finance\AnalyseResource;
use App\Models\Earning;
use App\Services\EarningService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class FinanceAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'slug' => ['sometimes', 'string', 'in:general,top-lists,platforms,countries'],
            'start_date' => ['date', 'required_with:end_date'],
            'end_date' => ['date', 'required_with:start_date'],
        ]);

        $start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::now()->endOfMonth()->format('Y-m-d');

        if (isset($request->start_date) && isset($request->end_date)) {
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d');
        }


        $tab = 'general';
        $tab = $request->input('slug') ?? $tab;
        $earning = Earning::with('product', 'song')->whereBetween('sales_date', [$start_date, $end_date])->get();
        $response = new AnalyseResource($earning, $tab);

        //dd($response->resolve());
        return inertia('Control/Finance/Analysis/Index', [
            'data' => $response->resolve()
        ]);
    }
}
