<?php

namespace App\Http\Controllers\Control;

use App\Enums\AlbumTypeEnum;
use App\Enums\AnnouncementReceiversEnum;
use App\Enums\AnnouncementTypeEnum;
use App\Enums\BroadcastPublishedCountryTypeEnum;
use App\Enums\BroadcastStatusEnum;
use App\Enums\BroadcastTypeEnum;
use App\Enums\PlatformTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Broadcast\BroadcastChangeStatusRequest;
use App\Http\Requests\Broadcast\BroadcastCorrectionRequest;
use App\Jobs\DDEXXmlJob;
use App\Models\Artist;
use App\Models\Broadcast;
use App\Models\Country;
use App\Models\Genre;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Upc;
use App\Services\AnnouncementServices;
use App\Services\BroadcastApplyServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class BroadcastApplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('broadcast_apply_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $broadcasts = Broadcast::when($request->status, function ($query) use ($request) {
            $query->where('status', $request->status);
        })
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->with('artists', 'label', 'publishedCountries')->advancedFilter();

        $statuses = BroadcastStatusEnum::getTitles();
        $statusTitles = BroadcastStatusEnum::getTitles();

        return inertia('Control/BroadcastsApply/Index', compact('broadcasts', 'statusTitles', 'statuses'));
    }


    public function edit(Broadcast $broadcast)
    {
        abort_if(Gate::denies('broadcast_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $props = [
            'broadcast' => $broadcast->load('artists', 'label', 'publishedCountries', 'genre', 'subGenre', 'language',
                'label', 'hashtags', 'downloadPlatforms', 'songs', 'addedBy'),
            'genres' => Genre::pluck('name', 'id'),
            'artists' => Artist::pluck('name', 'id'),
            'labels' => Label::pluck('name', 'id'),
            'counties' => Country::pluck('name', 'id'),
            'languages' => Country::pluck('name', 'id'),
            'platforms' => Platform::get()->groupBy('type')->map(function ($platforms) {
                return $platforms->pluck('name', 'id');
            }),
            'album_types' => AlbumTypeEnum::getTitles(),
            'platform_types' => PlatformTypeEnum::getTitles(),
            'broadcast_types' => BroadcastTypeEnum::getTitles(),
            'publish_country_types' => BroadcastPublishedCountryTypeEnum::getTitles(),
        ];

        return inertia('Control/BroadcastsApply/Edit', compact('props'));

    }


    public function changeStatus(BroadcastChangeStatusRequest $request)
    {

        abort_if(Gate::denies('broadcast_change_status'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $broadcast = Broadcast::where('id', $request->id)->firstOrFail();

        $newUpc = Upc::notUsedUpcs()->first();
        if ($newUpc) {
            $broadcast->upc_code = $broadcast->upc_code ?? $newUpc->upc;
            $newUpc->use_by_date = now();
            $newUpc->broadcast_id = $broadcast->id;
            $newUpc->save();
            $broadcast->upc_code = $newUpc->upc;
            $broadcast->save();
        }

        BroadcastApplyServices::update($broadcast, $request->validated());


        $data = [
            'name' => 'Broadcast Approved',
            'type' => [
                AnnouncementTypeEnum::EMAIl->value,
                AnnouncementTypeEnum::SMS->value,
                AnnouncementTypeEnum::SITE->value
            ],
            'content_type' => 'success',
            'content' => $broadcast->name.' '.__('panel.broadcast.successfully_published'),
            'receivers' => AnnouncementReceiversEnum::SELECTED->value,
            'selected' => [$broadcast->added_by],
            'url' => route('dashboard.broadcasts.show', $broadcast->id)
        ];

        AnnouncementServices::create($data);

        return redirect()->back()->with(['message' => 'Broadcast updated successfully'], Response::HTTP_OK);
    }

    public function correction(BroadcastCorrectionRequest $request)
    {

        abort_if(Gate::denies('broadcast_apply_correction'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $broadcast = Broadcast::where('id', $request->id)->firstOrFail();


        $data = $request->validated();
        $data['status'] = '3';
        BroadcastApplyServices::update($broadcast, $data);

        $data = [
            'name' => 'Broadcast Correction need',
            'type' => [
                AnnouncementTypeEnum::EMAIl->value,
                AnnouncementTypeEnum::SMS->value,
                AnnouncementTypeEnum::SITE->value
            ],
            'content_type' => 'warning',
            'content' => $broadcast->name.' '.$data['correction'],
            'receivers' => AnnouncementReceiversEnum::SELECTED->value,
            'selected' => [$broadcast->added_by],
            'url' => route('dashboard.broadcasts.show', $broadcast->id)
        ];

        AnnouncementServices::create($data);

        return redirect()->back()->with(['message' => 'Broadcast updated successfully'], Response::HTTP_OK);
    }

    public function makeDdexXml(Broadcast $broadcast)
    {
        abort_if(Gate::denies('broadcast_make_xml'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        DDEXXmlJob::dispatch($broadcast);

        return redirect()->back()->with([
            'notification' => [
                'type' => 'success',
                'message' => 'DDEX XML is being created. You will be notified when it is ready.'
            ]
        ]);
    }

    public function downloadDdexXml(Broadcast $broadcast)
    {
        abort_if(Gate::denies('broadcast_download_xml'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $path = $broadcast->xml_path;
        $file = Storage::disk('xml_files')->get($path);

        return response($file, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="'.$path.'"'
        ]);

    }
}
