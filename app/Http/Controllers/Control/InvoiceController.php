<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\CountryServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // abort_if(Gate::denies('invoice_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoices = Invoice::advancedFilter();

        return inertia('Control/Invoices/Index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $counties = CountryServices::get();
        return inertia('Control/Invoices/Create', compact('counties'));
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
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $counties = CountryServices::get();
        return inertia('Control/Invoices/Edit', compact('counties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
