<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Services\CountryServices;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class ContractController extends Controller
{

    public function index()
    {
        abort_if(Gate::denies('contract_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $contracts = Contract::advancedFilter();

        return inertia('Control/Contracts/Index', compact('contracts'));
    }

    public function search($search)
    {
        Contract::search($search);

        return response()->json($search, Response::HTTP_OK);
    }
}
