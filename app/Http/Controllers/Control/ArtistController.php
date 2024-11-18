<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Artist\ArtistStoreRequest;
use App\Http\Requests\Artist\ArtistUpdateRequest;
use App\Http\Resources\ArtistResource;
use App\Jobs\SpotifyImageUploadJob;
use App\Models\Artist;
use App\Models\ArtistBranch;
use App\Models\Platform;
use App\Services\ArtistServices;
use App\Services\CountryServices;
use App\Services\iTunesServices;
use App\Services\MediaServices;
use App\Services\SpotifyServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('artist_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $artists = Artist::with('artistBranches', 'platforms', 'country', 'products')
            ->when(request('status') == 1, function ($query) {
                $query->whereDoesntHave('products');
            })
            ->when(request('status') == 2, function ($query) {
                $query->whereHas('products');
            })
            ->when(request('s_date') && request('e_date'), function ($query) {
                $query->whereBetween('created_at', [request('s_date'), request('e_date')]);
            })
            ->advancedFilter();

        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'id', 'name', 'emoji');
        $countryCodes = CountryServices::getCountryPhoneCodes();
        $usedGenres = ArtistServices::usedGenres($artists);

        $filters = [
            [
                'title' => __('control.artist.fields.status'),
                'param' => 'status',
                'options' => [
                    [
                        'value' => 1,
                        'label' => __('control.artist.fields.status_inactive')
                    ],
                    [
                        'value' => 2,
                        'label' => __('control.artist.fields.status_active')
                    ],
                ],
            ],
            [
                'title' => __('control.artist.fields.genre'),
                'param' => 'genre',
                'options' => getDataFromInputFormat($usedGenres, 'id', 'name')
            ]
        ];
        $artistBranches = getDataFromInputFormat(ArtistBranch::all(), 'id', 'name');
        $platforms = getDataFromInputFormat(Platform::get(), 'id', 'name', 'icon');

        return inertia('Control/Artists/Index', [
            'artists' => ArtistResource::collection($artists)->resource,
            'countries' => $countries,
            'filters' => $filters,
            "artistBranches" => $artistBranches,
            "platforms" => $platforms,
            'countryCodes' => $countryCodes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('artist_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $countries = getDataFromInputFormat(CountryServices::get(), 'id', 'native', 'emoji');
        $platforms = getDataFromInputFormat(Platform::get(), 'id', 'name', 'image');
        $artistBranches = getDataFromInputFormat(ArtistBranch::all(), 'id', 'name');

        return inertia('Control/Artists/Create', compact('countries', 'platforms', 'artistBranches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArtistStoreRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $accept = $request->header('Accept');

        $artist = Artist::create($data);

        $artist->artistBranches()->sync($request->input('artist_branches', []));

        foreach ($request->input('platforms', []) as $platform) {
            $artist->platforms()->syncWithoutDetaching(
                [
                    $platform['value'] => ['url' => $platform['url']]
                ]
            );
        }

        if ($request->hasFile('image')) {
            MediaServices::upload($artist, $request->file('image'), 'artists', 'artists');
        } elseif ($artist->platforms && $artist->platforms->contains('code', 'spotify')) {
            SpotifyImageUploadJob::dispatch($artist);
        }
        $props = [
            'type' => 'success',
            'message' => __('control.notification_created', ['model' => __('control.artist.title_singular')]),
            'data' => new ArtistResource($artist),
        ];
        if ($accept === 'application/json') {


            return response()->json($props, Response::HTTP_OK);
        } else {
            return redirect()->route('control.catalog.artists.index')->with(
                [
                    'notification' => $props
                ]
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Artist $artist)
    {
        abort_if(Gate::denies('artist_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $artist->load('artistBranches', 'platforms', 'country', 'products.songs');
        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'id', 'name', 'emoji');
        $artistBranches = getDataFromInputFormat(ArtistBranch::all(), 'id', 'name');
        $platforms = getDataFromInputFormat(Platform::get(), 'id', 'name', 'icon');

        return inertia('Control/Artists/Show', compact('artist', 'countries', 'artistBranches', 'platforms'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Artist $artist)
    {
        abort_if(Gate::denies('artist_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $countries = getDataFromInputFormat(CountryServices::get(), 'id', 'name', 'emoji');
        $platforms = getDataFromInputFormat(Platform::get(), 'id', 'name', 'image');
        $artistBranches = getDataFromInputFormat(ArtistBranch::all(), 'id', 'name');

        $artist->load('artistBranches', 'platforms', 'country');

        return inertia('Control/Artists/Edit', compact('artist', 'countries', 'platforms', 'artistBranches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ArtistUpdateRequest $request, Artist $artist)
    {
        $artist->update($request->validated());

        if (isset($request->validated()['platforms']) && $request->validated()['platforms']) {
            foreach ($request->validated()['platforms'] as $platform) {
                $artist->platforms()->syncWithoutDetaching([$platform['value'] => ['url' => $platform['url']]]);
            }
        } else {
            $artist->platforms()->detach();
        }

        $artist->artistBranches()->syncWithoutDetaching($request->validated()['artist_branches'], []);


        if ($request->hasFile('image')) {
            MediaServices::upload($artist, $request->file('image'), 'artists', 'artists');
        } elseif ($artist->image == null && $artist->platforms && $artist->platforms->contains('code', 'spotify')) {
            SpotifyImageUploadJob::dispatch($artist);
        }

        return redirect()->route('control.catalog.artists.index')->with(
            [
                'notification' => [
                    'type' => 'success',
                    'message' => __('control.notification_updated', ['model' => __('control.artist.title_singular')]),
                    'data' => new ArtistResource($artist),
                ]
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artist $artist, Request $request)
    {
        abort_if(Gate::denies('artist_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $accept = $request->header('Accept');
        $artist->delete();


        //TODO Code Refactor
        $notification = [
            'type' => 'success',
            'message' => __('control.notification_deleted', ['model' => __('control.artist.title_singular')])
        ];


        if ($accept === 'application/json') {
            return response()->json($notification, Response::HTTP_OK);
        } else {
            return redirect()->route('control.catalog.artists.index')->with(
                [
                    $notification
                ]
            );
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|max:255'
        ]);

        $search = $request->input('search');

        $labels = ArtistServices::search($search);

        return response()->json($labels, Response::HTTP_OK);
    }

    public function searchPlatform(Request $request)
    {
        $request->validate([
            'search' => 'required|string|max:255'
        ]);


        $search = $request->input('search');

        $Itunes = iTunesServices::search($search);
        $spotify = SpotifyServices::search($search, 'artist');


        $data = [
            'itunes' => $Itunes,
            'spotify' => $spotify,
        ];


        return response()->json($data, Response::HTTP_OK);
    }
}
