<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;

use App\Services\CityServices;
use App\Services\CountryServices;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CityController extends Controller
{


    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);

        $search = $request->input('search');

        $cities = CityServices::search($search);

        return response()->json($cities, Response::HTTP_OK);
    }
}
