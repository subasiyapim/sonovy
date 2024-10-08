<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class EarningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('earning_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $earnings = Earning::with('report.file.user', 'report.user', 'report.song.broadcasts.artists', 'label.user')
            ->when(isset($request->dates), function ($query) use ($request) {
                $query->whereBetween('report_date', [$request->dates['0'], $request->dates['1']]);
            })
            ->advancedFilter();

        return inertia('Control/Earnings/Index', compact('earnings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Earning $reserve)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Earning $reserve)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Earning $reserve)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Earning $reserve)
    {
        //
    }
}
