<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanItem\PlanItemRequest;
use App\Models\PlanItem;
use App\Models\Translations\PlanItemTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PlanItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('plan_item_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $planItems = PlanItem::advancedFilter();

        return inertia('Control/Plans/PlanItems/Index', compact('planItems'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PlanItemRequest $request)
    {
        $planItem = PlanItem::create($request->except('translations'));

        foreach ($request->validated()['translations'] as $key => $row) {
            DB::table('plan_item_translations')->insert([
                'plan_item_id' => $planItem->id,
                'locale' => $key,
                'name' => $row['name'],
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlanItemRequest $request, PlanItem $planItem)
    {
        $data = $request->except('translations');

        foreach ($request->validated()['translations'] as $key => $value) {
            $data[$key] = $value;
        }

        $planItem->update($data);

        return redirect()->back()->with([
            'notification' => [
                'type' => 'success', 'message' => 'Plan item updated successfully'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanItem $planItem)
    {
        $planItem->delete();

        return redirect()->back()->with([
            'notification' => [
                'type' => 'success', 'message' => 'Plan item deleted successfully'
            ]
        ]);
    }
}
