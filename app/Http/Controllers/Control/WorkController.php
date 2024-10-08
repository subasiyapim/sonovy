<?php

namespace App\Http\Controllers\Control;

use App\Enums\AlbumTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use App\Models\Contract;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class WorkController extends Controller
{

    public function index(Request $request)
    {
        abort_if(Gate::denies('work_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $works = Song::with(['participants.user', 'broadcasts'])
            ->whereHas('broadcasts', function ($query) use ($request) {
                $query->where('album_type', $request->tab && $request->tab === 'albums' ? 2 : 1);
            })
            ->advancedFilter();


        // $broadcast = Broadcast::advancedFilter();
        return inertia('Control/Works/Index', compact('works'));
    }

    public function search($search)
    {
        Contract::search($search);

        return response()->json($search, Response::HTTP_OK);

    }
}
