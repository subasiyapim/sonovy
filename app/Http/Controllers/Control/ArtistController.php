<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Platform;
use App\Services\ArtistBranchServices;
use App\Services\CountryServices;
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

        $artists = Artist::with('artistBranches')->advancedFilter();

        return inertia('Control/Artists/Index', compact('artists'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('broadcast_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $countries = CountryServices::get();
        $platforms = Platform::all();
        $artistBranches = ArtistBranchServices::getBranchesFromInputFormat();

        return inertia('Control/Artists/Create', compact('countries', 'platforms', 'artistBranches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Artist $artist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Artist $artist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Artist $artist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artist $artist)
    {
        //
    }
}
