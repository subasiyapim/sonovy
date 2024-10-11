<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Artist\ArtistStoreRequest;
use App\Http\Requests\Artist\ArtistUpdateRequest;
use App\Http\Resources\ArtistResource;
use App\Models\Artist;
use App\Models\ArtistBranch;
use App\Models\Platform;
use App\Services\CountryServices;
use App\Services\MediaServices;
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

        $artists = ArtistResource::collection(Artist::with('artistBranches')->advancedFilter())->resource;
        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'id', 'name', 'emoji');


        return inertia('Control/Artists/Index', compact('artists','countries'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('artist_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $countries = getDataFromInputFormat(CountryServices::get(), 'id', 'name', 'emoji');
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
        $data['added_by'] = auth()->id();

        $artist = Artist::create($data);

        $artist->artistBranches()->sync($request->input('artist_branches', []));

        if ($request->hasFile('image')) {
            MediaServices::upload($artist, $request->file('image'), 'artists');
        }

        return redirect()->route('control.artists.index')->with(
            [
                'notification' => [
                    'type' => 'success',
                    'message' => __('control.notification_created', ['model' => __('control.artist.title_singular')])
                ]
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Artist $artist)
    {
        abort_if(Gate::denies('artist_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $artist->load('artistBranches');

        return inertia('Control/Artists/Show', compact('artist'));
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

        $artist->load('artistBranches', 'platforms');

        return inertia('Control/Artists/Edit', compact('artist', 'countries', 'platforms', 'artistBranches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ArtistUpdateRequest $request, Artist $artist)
    {
        $data = $request->validated();
        $data['added_by'] = auth()->id();

        $artist = Artist::create($data);

        $artist->artistBranches()->sync($request->input('artist_branches', []));

        if ($request->hasFile('image')) {
            MediaServices::upload($artist, $request->file('image'), 'artists');
        }

        return redirect()->route('control.artists.index')->with(
            [
                'notification' => [
                    'type' => 'success',
                    'message' => __('control.notification_updated', ['model' => __('control.artist.title_singular')])
                ]
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artist $artist)
    {
        abort_if(Gate::denies('artist_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $artist->delete();

        return redirect()->route('control.artists.index')->with(
            [
                'notification' => [
                    'type' => 'success',
                    'message' => __('control.notification_deleted', ['model' => __('control.artist.title_singular')])
                ]
            ]
        );
    }
}
