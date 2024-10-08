<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\CountryServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CountryController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('country_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $countries = Country::all();

        return inertia('Control/Countries/Index', [
            'countries' => $countries
        ]);
    }

    public function show($id)
    {
        abort_if(Gate::denies('country_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('country_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    }

    public function update(Request $request, Country $country)
    {
        CountryServices::update($request, $country);

        return to_route('panel.countries.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);

        $search = $request->input('search');

        $countries = CountryServices::search($search);

        return response()->json($countries, Response::HTTP_OK);
    }
}
