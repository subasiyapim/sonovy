<?php

namespace App\Http\Controllers\Control;

use App\Enums\PlatformTypeEnum;
use App\Http\Controllers\Controller;

use App\Http\Requests\Platform\PlatformMatchRequest;
use App\Http\Requests\Platform\PlatformRequest;
use App\Models\Platform;
use App\Services\MediaServices;
use App\Services\PlatformService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PlatformController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('platform_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $platforms = Platform::when($request->has('type'), function ($query) use ($request) {
            $query->where('type', $request->input('type'));
        })->advancedFilter();


        $types = PlatformTypeEnum::getTitlesFromInputFormat();

        return inertia('Control/Platforms/Index', compact('platforms', 'types'));
    }

    public function create()
    {
        abort_if(Gate::denies('platform_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $types = PlatformTypeEnum::getTitlesFromInputFormat();

        return inertia('Control/Platforms/Create', compact('types'));
    }

    public function store(PlatformRequest $request)
    {
        $data = $request->validated();
        $data['code'] = Str::slug($data['name'].'-'.Str::random(5));

        $platform = Platform::create($data);

        if ($request->hasFile('image')) {
            MediaServices::upload($platform, $request->file('image'), null, null, 'platforms', 'platforms');
        }

        return redirect()
            ->route('dashboard.platforms.index')
            ->with([
                'notification' => [
                    'type' => 'success',
                    'title' => 'Success!',
                    'message' => 'Platform created successfully.'
                ]
            ]);
    }

    public function edit(Platform $platform)
    {
        abort_if(Gate::denies('integration_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $types = PlatformTypeEnum::getTitlesFromInputFormat();

        return inertia('Control/Platforms/Edit', compact('platform', 'types'));
    }

    public function update(PlatformRequest $request, Platform $platform)
    {
        $platform->update($request->validated());

        if ($request->hasFile('image')) {
            MediaServices::upload($platform, $request->file('image'), null, null, 'platforms', 'platforms');
        }

        return redirect()
            ->route('dashboard.platforms.index')
            ->with([
                'notification' => [
                    'type' => 'success',
                    'title' => 'Success!',
                    'message' => 'Platform updated successfully.'
                ]
            ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);

        $search = $request->input('search');

        $labels = PlatformService::search($search);

        return response()->json($labels, Response::HTTP_OK);
    }

    public function match(PlatformMatchRequest $request)
    {
        $slave = Platform::find($request->input('slave_id'));

        $artist_platform = DB::table('artist_platform')->where('platform_id', $request->validated()['slave_id'])->get();
        $platform_user = DB::table('platform_user')->where('platform_id', $request->validated()['slave_id'])->get();
        $broadcast_download_platform = DB::table('broadcast_download_platform')->where('platform_id',
            $request->validated()['slave_id'])->get();
        $earnings = DB::table('earnings')->where('platform_id', $request->validated()['slave_id'])->get();

        $artist_platform->each(function () use ($request) {
            DB::table('artist_platform')->where('platform_id',
                $request->input('slave_id'))->update(['platform_id' => $request->input('master_id')]);
        });

        $platform_user->each(function () use ($request) {
            DB::table('platform_user')->where('platform_id',
                $request->input('slave_id'))->update(['platform_id' => $request->input('master_id')]);
        });

        $broadcast_download_platform->each(function () use ($request) {
            DB::table('broadcast_download_platform')->where('platform_id',
                $request->input('slave_id'))->update(['platform_id' => $request->input('master_id')]);
        });

        $earnings->each(function () use ($request) {
            DB::table('earnings')->where('platform_id',
                $request->input('slave_id'))->update(['platform_id' => $request->input('master_id')]);
        });

        $slave->delete();

        return redirect()
            ->route('dashboard.platforms.index')
            ->with([
                'notification' => [
                    'type' => 'success',
                    'title' => 'Success!',
                    'message' => 'Platform matched successfully.'
                ]
            ]);

    }
}
