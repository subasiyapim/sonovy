<?php

namespace App\Http\Controllers\Control;

use App\Enums\AlbumTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Label\LabelStoreRequest;
use App\Http\Requests\Label\LabelUpdateRequest;
use App\Models\DspLabel;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Product;
use App\Services\CountryServices;
use App\Services\LabelServices;
use App\Services\MediaServices;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LabelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('artist_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $labels = Label::with('country', 'products', 'user')
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
                'options' => getDataFromInputFormat(
                    \App\Models\System\Country::get(['id', 'name', 'emoji']),
                    'id',
                    'name',
                    'emoji'
                )
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
        $label = Label::create($data);

        if ($request->hasFile('image')) {
            MediaServices::upload($label, $request->file('image'), 'labels', 'labels');
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
    public function show(Request $request, Label $label)
    {
        abort_if(Gate::denies('label_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = Auth::user();


        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'id', 'name', 'emoji');
        $countryCodes = CountryServices::getCountryPhoneCodes();
        $tab = [];
        if ($request->slug == "products") {
            $products = Product::with('artists', 'mainArtists', 'label', 'songs', 'downloadPlatforms')
                ->where('label_id', '=', $label->id)
                ->when($request->input('status'), function ($query) use ($request) {
                    $query->where('status', $request->input('status'));
                })
                ->when($request->input('type'), function ($query) use ($request) {
                    $query->where('type', $request->input('type'));
                })->get();



            $products->each(function ($product) {

                $product->format = AlbumTypeEnum::from($product->format_id)->title();
            });
            $tab['products'] = $products;
        }

        if ($request->slug == "dsp") {

            $dsp = $label->dspLabels()
                ->with('platform')  // Assuming DpsLabel has a 'platform' relationship
                ->when($request->input('status'), function ($query) use ($request) {
                    $query->where('status', $request->input('status'));
                })
                ->get()
                ->map(function ($dspLabel) {
                    // Access the 'status_label' attribute to get the status text
                    $dspLabel->status_text = $dspLabel->status_label;
                    return $dspLabel;
                });
            $tab['dsp'] = $dsp;
            if ($user->roles[0]->code == "user") {
                $platforms = Platform::get();
                $tab['platforms'] = $platforms;
            }
        }

        $label->loadMissing('country', 'products.songs', 'user');

        return inertia('Control/Labels/Show', compact('label', 'countries', 'countryCodes', 'tab'));
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
            MediaServices::upload($label, $request->file('image'), 'labels', 'labels');
        }

        $label->loadMissing('country', 'products.songs', 'user');
        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Plak şirketi başarıyla güncellendi',
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


    public function changeStatus(Request $request, Label $label)
    {

        $validated = $request->validate([
            'status' => 'required|in:1,2,3',
            'dsp_ids' => 'required|array',
            'dsp_ids.*' => 'integer|exists:dsp_label,id',
        ]);

        $dsp_ids = $validated['dsp_ids'];
        $status = $validated['status'];

        $dspLabels = $label->dspLabels()->whereIn('id', $dsp_ids)->get();

        if ($dspLabels->isEmpty()) {
            return response()->json(['message' => 'Herhangi bir dsp bulunamadı.'], 404);
        }

        foreach ($dspLabels as $dspLabel) {
            $dspLabel->status = $status;
            $dspLabel->save();
        }

        // Return a success response
        return response()->json([
            'message' => 'DspLabel status updated successfully.',
            'dspLabels' => $dspLabels,
        ]);
    }

    public function createDSP(Request $request, Label $label)
    {
        $validated = $request->validate([
            'platforms' => 'required|array',
            'platforms.*' => 'integer|exists:platforms,id',
        ]);

        $data = collect($validated['platforms'])->map(fn($platformId) => [
            "label_id" => $label->id,
            "platform_id" => $platformId,
            "created_at" => now(),
            "updated_at" => now(),
        ])->toArray();

        DspLabel::insert($data);
        $dsp = $label->dspLabels()
            ->with('platform')  // Assuming DpsLabel has a 'platform' relationship
            ->when($request->input('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->get()
            ->map(function ($dspLabel) {
                // Access the 'status_label' attribute to get the status text
                $dspLabel->status_text = $dspLabel->status_label;
                return $dspLabel;
            });
        return response()->json([
            'dsp' => $dsp,
            'message' => 'DSPs successfully added to the label.',
        ], 201);
    }
}
