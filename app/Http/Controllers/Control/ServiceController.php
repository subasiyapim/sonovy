<?php

namespace App\Http\Controllers\Control;


use App\Http\Controllers\Controller;

use App\Services\ServiceServices;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class  ServiceController extends Controller
{


    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);

        $search = $request->input('search');
        $services = ServiceServices::search($search);


        return response()->json($services, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        // $service = ServiceServices::create($request->validated());
        $service = ServiceServices::create(["name" => $request->name]);

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Service created successfully',
                'data' => $service
            ]
        ]);
    }


}
