<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;

use App\Services\StateServices;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StateController extends Controller
{


    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);

        $search = $request->input('search');

        $states = StateServices::search($search);

        return response()->json($states, Response::HTTP_OK);
    }
}
