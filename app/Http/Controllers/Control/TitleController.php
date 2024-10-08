<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Title\TitleStoreRequest;
use App\Http\Requests\Title\TitleUpdateRequest;
use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class TitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titles = Title::advancedFilter();
        return inertia('Control/Titles/Index', compact('titles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Control/Titles/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TitleStoreRequest $request)
    {
        Title::create($request->validated()["translations"]);

        return to_route('dashboard.titles.index')
            ->with(['notification' => __('panel.notification_created', ['model' => __('panel.title.title_singular')])]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Title $title)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Title $title)
    {
        return inertia('Control/Titles/Edit', compact('title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TitleUpdateRequest $request, Title $title)
    {
        $title->update($request->validated()["translations"]);

        return to_route('dashboard.titles.index')
            ->with(['notification' => __('panel.notification_updated', ['model' => __('panel.title.title_singular')])]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Title $title)
    {
        abort_if(Gate::denies('title_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $title->delete();

        return to_route('dashboard.titles.index')
            ->with(['notification' => __('panel.notification_deleted', ['model' => __('panel.title.title_singular')])]);
    }
}
