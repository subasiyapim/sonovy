<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiteFeature\SiteFeatureStoreRequest;
use App\Http\Requests\SiteFeature\SiteFeatureUpdateRequest;
use App\Models\SiteFeature;
use App\Services\SiteFeatureServices;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('site_feature_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $site_features = SiteFeature::advancedFilter();

        return inertia('Control/SiteFeatures/Index', compact('site_features'));

    }


    public function create()
    {

        return inertia('Control/SiteFeatures/Create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(SiteFeatureStoreRequest $request)
    {
        $site_feature = SiteFeatureServices::create($request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Site Feature created successfully',
                'data' => $site_feature
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SiteFeature $site_feature)
    {
        abort_if(Gate::denies('site_feature_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($site_feature, Response::HTTP_OK);
    }

    public function edit(SiteFeature $site_feature)
    {

        abort_if(Gate::denies('site_feature_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return inertia('Control/SiteFeatures/Edit', compact('site_feature'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SiteFeatureUpdateRequest $request, SiteFeature $site_feature)
    {

        SiteFeatureServices::update($site_feature, $request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Site Feature updated successfully',
                'data' => $site_feature
            ]
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);


        $search = $request->input('search');

        $site_features = SiteFeatureServices::search($search);

        return response()->json($site_features, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SiteFeature $site_feature)
    {
        abort_if(Gate::denies('site_feature_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $site_feature->delete();

        return redirect()->back();
    }
}
