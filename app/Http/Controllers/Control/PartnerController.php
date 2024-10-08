<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\PartnerStoreRequest;
use App\Http\Requests\Partner\PartnerUpdateRequest;
use App\Models\Partner;
use App\Services\PartnerServices;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('partner_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $partners = Partner::advancedFilter();

        return inertia('Control/Partners/Index', compact('partners'));

    }


    public function create()
    {

        return inertia('Control/Partners/Create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PartnerStoreRequest $request)
    {
        $partner = PartnerServices::create($request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Partner created successfully',
                'data' => $partner
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Partner $partner)
    {
        abort_if(Gate::denies('partner_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($partner->load('media'), Response::HTTP_OK);
    }

    public function edit(Partner $partner)
    {

        abort_if(Gate::denies('partner_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return inertia('Control/Partners/Edit', compact('partner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PartnerUpdateRequest $request, Partner $partner)
    {

        PartnerServices::update($partner, $request->validated());

        return redirect()->route('dashboard.partners.index')
            ->with([
                'notification' => [
                    'title' => 'Success',
                    'message' => 'Partner updated successfully',
                    'data' => $partner
                ]
            ]);

    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);


        $search = $request->input('search');

        $partners = PartnerServices::search($search);

        return response()->json($partners, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partner $partner)
    {
        abort_if(Gate::denies('partner_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $partner->delete();

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Partner başarıyla silindi.',
            ]
        ]);
    }
}
