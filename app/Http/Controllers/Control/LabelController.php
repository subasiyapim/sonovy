<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Label\LabelStoreRequest;
use App\Http\Requests\Label\LabelUpdateRequest;
use App\Models\Label;
use App\Services\CountryServices;
use App\Services\LabelServices;
use App\Services\MediaServices;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LabelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('artist_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $labels = Label::with('country', 'products')
            ->when(request('status') == 1, function ($query) {
                $query->whereDoesntHave('products');
            })
            ->when(request('status') == 2, function ($query) {
                $query->whereHas('products');
            })
            ->when(request()->country, function ($query) {
                if (request()->country != 'Tümü') {
                    $query->where('country_id', request()->country);
                }
            })
            ->advancedFilter();

        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'id', 'name', 'emoji');
        $countryCodes = CountryServices::getCountryPhoneCodes();
        $filters = [
            [
                'title' => __('control.artist.fields.status'),
                'param' => 'status',
                'options' => [
                    [
                        'value' => 1,
                        'label' => __('control.label.fields.status_inactive')
                    ],
                    [
                        'value' => 2,
                        'label' => __('control.label.fields.status_active')
                    ],
                ],
            ],
            [
                'title' => __('control.label.fields.country_id'),
                'param' => 'country',
                'options' => getDataFromInputFormat(\App\Models\System\Country::get(['id', 'name', 'emoji']), 'id',
                    'name', 'emoji')
            ]
        ];
        return inertia('Control/Labels/Index', compact('labels', 'countries', 'countryCodes', 'filters'));
    }


    public function create()
    {
        abort_if(Gate::denies('label_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'name', 'id', 'emoji');


        return inertia('Control/Labels/Create', compact('countries'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(LabelStoreRequest $request)
    {
        $data = $request->validated();
        $data['added_by'] = auth()->id();
        $label = Label::create($data);

        if ($request->hasFile('image')) {
            MediaServices::upload($label, $request->image['file'], 'labels');
        }
        
        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Label created successfully',
                'data' => $label
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Label $label)
    {
        abort_if(Gate::denies('label_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'id', 'name', 'emoji');

        $label->load('country', 'products.songs', 'user');
        return inertia('Control/Labels/Show', compact('label', 'countries'));
    }

    public function edit(Label $label)
    {
        abort_if(Gate::denies('label_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'name', 'id', 'emoji');

        return inertia('Control/Labels/Edit', compact('label', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LabelUpdateRequest $request, Label $label)
    {
        $label->update($request->validated());

        if ($request->hasFile('image')) {
            MediaServices::upload($label, $request->image['file'], 'labels');
        }

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Label updated successfully',
                'data' => $label
            ]
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|max:255'
        ]);


        $search = $request->input('search');

        $labels = LabelServices::search($search);

        return response()->json($labels, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Label $label, Request $request)
    {
        abort_if(Gate::denies('label_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $accept = $request->header('Accept');
        $label->delete();


        //TODO Code Refactor
        $notification = [
            'type' => 'success',
            'message' => __('control.notification_deleted', ['model' => __('control.label.title_singular')])
        ];


        if ($accept === 'application/json') {
            return response()->json($notification, Response::HTTP_OK);
        } else {
            return redirect()->route('control.catalog.labels.index')->with(
                [
                    $notification
                ]
            );
        }
    }
}
