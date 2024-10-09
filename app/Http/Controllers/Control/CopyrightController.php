<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Copyright\CopyrightStoreRequest;
use App\Models\Copyright;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CopyrightController extends Controller
{


    public function index()
    {

        abort_if(Gate::denies('copyright_list') && Gate::denies('copyright_management'), Response::HTTP_FORBIDDEN,
            '403 Forbidden');

        $copyrights = Copyright::with('copyrightSongs', 'product')
            ->advancedFilter();

        return inertia('Control/Copyrights/Index', compact('copyrights'));
    }

    public function demand()
    {
        abort_if(Gate::denies('copyright_demand'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $copyright_types = Copyright::$COPYRIGHT_TYPES;
        $platforms_to_reject = Copyright::$PLATFORM_TO_REJECT;

        return inertia('Control/Copyrights/RejectionDemand', compact('copyright_types', 'platforms_to_reject'));
    }

    public function store(CopyrightStoreRequest $request): \Illuminate\Http\RedirectResponse
    {
        $copyright = Copyright::create(
            [
                'broadcast_type' => $request->broadcast_type,
                'platform' => $request->platform,
                'type' => $request->type,
                'product_id' => $request->product_id,
            ]
        );

        foreach ($request->songs as $song) {
            $copyright->copyrightSongs()->create(
                [
                    'song_id' => $song['id'],
                    'url' => $song['url'],
                ]
            );
        }

        return redirect()->back();
    }

}
