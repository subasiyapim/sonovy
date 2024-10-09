<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feature\FeatureStoreRequest;
use App\Http\Requests\Feature\FeatureUpdateRequest;
use App\Models\Feature;
use App\Models\PlanItem;
use App\Services\FeatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('feature_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $features = Feature::advancedFilter();
        $period = FeatureService::getPeriodsFromInputFormat();
        $items = PlanItem::active()->where('category', '1')->get(); // Publish

        return inertia('Control/Features/Index', compact('features', 'period', 'items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('feature_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return inertia('Control/Features/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FeatureStoreRequest $request)
    {

        $feature = Feature::create(
            [
                'plan_item_id' => $request->item['id'],
                'period' => $request->period,
                'limit' => $request->limit,
                'type' => $request->item['type'],
                'is_active' => $request->is_active,
                'amount' => $request->amount,
                'title' => $request->title,
                'description' => $request->description,
            ]
        );

        return to_route('dashboard.features.index')
            ->with(
                [
                    'notification' => __('control.notification_created',
                        ['model' => __('control.feature.title_singular')])
                ]
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(Feature $feature)
    {
        abort_if(Gate::denies('feature_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return inertia('Control/Features/Show', compact('feature'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feature $feature)
    {
        abort_if(Gate::denies('feature_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return inertia('Control/Features/Edit', compact('feature'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FeatureUpdateRequest $request, Feature $feature)
    {
        $feature->update(
            [
                'period' => $request->period,
                'limit' => $request->limit,
                'is_active' => $request->is_active,
                'amount' => $request->amount,
                'title' => $request->title,
                'description' => $request->description,
            ]
        );

        /*$feature->translations()->delete();

        foreach ($request->translations as $translation) {
            $feature->translations()->create($translation);
        }*/

        return to_route('dashboard.features.index')
            ->with(
                [
                    'notification' => __('control.notification_updated',
                        ['model' => __('control.feature.title_singular')])
                ]
            );
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feature $feature)
    {
        abort_if(Gate::denies('feature_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $feature->delete();

        return to_route('dashboard.features.index')
            ->with(
                [
                    'notification' => __('control.notification_deleted',
                        ['model' => __('control.feature.title_singular')])
                ]
            );
    }
}
