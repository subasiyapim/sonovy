<?php

namespace App\Http\Controllers\Control;


use App\Http\Controllers\Controller;
use App\Models\Service;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class  ExtraServiceController extends Controller
{

    public function index()
    {
        // abort_if(Gate::denies('invoice_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $invoices = Order::advancedFilter();

        return inertia('Control/ExtraServices/Index');

    }


    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);

        $search = $request->input('search');

        //TODO SERVICE SEARCH
        $services = [];


        return response()->json($services, Response::HTTP_OK);
    }


}
