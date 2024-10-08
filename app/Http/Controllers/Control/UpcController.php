<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Upc\UpcStoreRequest;
use App\Imports\UpcFileImport;
use App\Models\Upc;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UpcController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $used_upcs = Upc::usedUpcs()->with('broadcast')->orderBy('id', 'asc')->advancedFilter();
        $not_used_upcs = Upc::notUsedUpcs()->orderBy('id', 'asc')->advancedFilter();

        return inertia('Control/Upcs/Index', compact('used_upcs', 'not_used_upcs'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(UpcStoreRequest $request)
    {
        if ($request->input('type') == 'upc_file') {
            Excel::import(new UpcFileImport, $request->file('file'));
            return redirect()->route('dashboard.upcs.index')->with('success', 'UPC listesi başarıyla yüklendi.');
        }

        $upcs = explode("\n", $request->input('upcs'));

        foreach ($upcs as $upc) {
            Upc::create(['upc' => $upc]);
        }

        return redirect()->route('dashboard.upcs.index')->with('success', 'UPC listesi başarıyla yüklendi.');
    }

    public function destroy(Upc $upc)
    {
        $upc->delete();

        return redirect()->route('dashboard.upcs.index')->with(
            [
                'notification' => [
                    'group' => 'default',
                    'type' => 'success',
                    'title' => 'Success!',
                    'message' => 'UPC deleted successfully.'
                ]
            ]
        );
    }
}
