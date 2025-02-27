<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\PlanStoreRequest;
use App\Http\Requests\Plan\PlanUpdateRequest;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\PlanItem;
use App\Models\Translations\PlanTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('plan_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $plans = Plan::with('items')->advancedFilter();

        $plan_items = PlanItem::all();

        return inertia('Control/Management/Plans/index', compact('plans', 'plan_items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('plan_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $items = PlanItem::active()->get();

        return inertia('Control/Plans/Create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlanStoreRequest $request)
    {
        $plan = Plan::create($request->except('translations', 'items'));

        foreach ($request->all()['translations'] as $row) {
            DB::table('plan_translations')->insert([
                'plan_id' => $plan->id,
                'locale' => $row['locale'],
                'name' => $row['name'],
                'description' => $row['description'],
            ]);
        }

        foreach ($request->all()['items'] as $item) {
            if ($item['value'] != null) {
                $plan->items()->attach($item['id'], [
                    'value' => $item['value'] == 'Limitsiz' ? 999999 : $item['value'],
                    'note' => isset($item['note']) ? (is_array($item['note']) ? json_encode($item['note']) : $item['note']) : null
                ]);
            }
        }

        return to_route('dashboard.plans.index')
            ->with(
                ['notification' => __('control.notification_created', ['model' => __('control.plan.title_singular')])]
            );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan)
    {
        abort_if(Gate::denies('plan_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $plan->load('items');

        $items = PlanItem::active()->get();

        return inertia('Control/Plans/Edit', compact('plan', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlanUpdateRequest $request, Plan $plan)
    {
        $plan->update($request->except('translations', 'features'));

        $plan->translations()->delete();

        foreach ($request->validated()['translations'] as $key => $row) {
            DB::table('plan_translations')->insert([
                'plan_id' => $plan->id,
                'locale' => $key,
                'name' => $row['name'],
                'description' => $row['description'],
            ]);
        }

        $plan->items()->detach();

        foreach ($request->all()['items'] as $item) {
            if ($item['value'] != null) {
                $plan->items()->attach($item['id'], [
                    'value' => $item['value'] == 'Limitsiz' ? 999999 : $item['value'],
                    'note' => isset($item['note']) ? (is_array($item['note']) ? json_encode($item['note']) : $item['note']) : null
                ]);
            }
        }

        return to_route('dashboard.plans.index')
            ->with(
                ['notification' => __('control.notification_updated', ['model' => __('control.plan.title_singular')])]
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan)
    {
        abort_if(Gate::denies('plan_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $plan->translations()->delete();
        $plan->items()->detach();
        $plan->delete();

        return to_route('dashboard.plans.index')
            ->with(
                ['notification' => __('control.notification_deleted', ['model' => __('control.plan.title_singular')])]
            );
    }

    public function preferredPlan(Plan $plan): \Illuminate\Http\RedirectResponse
    {
        Plan::whereNot('id', $plan->id)->update(['preferred' => 0]);

        $plan->update(['preferred' => 1]);

        return to_route('dashboard.plans.index')
            ->with(
                ['notification' => __('control.notification_updated', ['model' => __('control.plan.title_singular')])]
            );
    }
}
