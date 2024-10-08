<?php

namespace App\Http\Controllers\Control;

use App\Enums\AlbumTypeEnum;
use App\Enums\BroadcastPublishedCountryTypeEnum;
use App\Enums\BroadcastStatusEnum;
use App\Enums\BroadcastTypeEnum;
use App\Enums\YoutubeChannelThemeEnum;
use App\Enums\PlatformTypeEnum;
use App\Events\NewBroadcastEvent;
use App\Exports\BroadcastExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Broadcast\BroadcastStoreRequest;
use App\Http\Requests\Broadcast\BroadcastUpdateRequest;
use App\Http\Requests\Broadcast\ConvertAudioRequest;
use App\Http\Resources\Panel\CountryResource;
use App\Jobs\ConvertAudioJob;
use App\Models\Artist;
use App\Models\Broadcast;
use App\Models\ConvertAudio;
use App\Models\Country;
use App\Models\Genre;
use App\Models\Label;
use App\Models\Participant;
use App\Models\Platform;
use App\Models\Song;
use App\Models\User;
use App\Services\ArtistBranchServices;
use App\Services\ArtistServices;
use App\Services\CatalogNumberService;
use App\Services\FFMpegServices;
use App\Services\ISRCServices;
use App\Services\iTunesServices;
use App\Services\MusicBrainzServices;
use App\Services\TimezoneService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\BroadcastServices;
use App\Services\CountryServices;
use App\Services\LanguageServices;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\ResponseFactory;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class BroadcastController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response|ResponseFactory
    {

        abort_if(Gate::denies('broadcast_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $broadcasts = Broadcast::with('artists', 'label', 'publishedCountries', 'songs')
            ->when($request->input('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->input('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->advancedFilter();
        $country_count = Country::count();
        $statuses = BroadcastStatusEnum::getTitles();
        $types = BroadcastTypeEnum::getTitles();

        return inertia('Control/Broadcasts/Index', compact('broadcasts', 'country_count', 'statuses', 'types'));
    }

    public function create(): \Inertia\Response|ResponseFactory
    {
        abort_if(Gate::denies('broadcast_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $genres = Genre::pluck('name', 'id');
        $artists = Artist::pluck('name', 'id');
        $labels = Label::pluck('name', 'id');
        $counties = CountryServices::get();
        $languages = LanguageServices::get();
        $users = User::pluck('name', 'id');
        $platform_types = PlatformTypeEnum::getTitles();
        $broadcast_types = BroadcastTypeEnum::getTitles();

        //TODO veritabanına eklenecek settingslere olabilir
        $barcode_type = 1;
        $platforms = Platform::get()->groupBy('type')->map(function ($platforms) {
            return $platforms->pluck('name', 'id');
        });

        $all_platforms = Platform::all();
        $publish_country_types = BroadcastPublishedCountryTypeEnum::getTitles();

        $platform_download_price = Platform::$PLATFORM_DOWNLOAD_PRICE;

        $artistBranches = ArtistBranchServices::getBranchesFromInputFormat();

        $availablePlanItems = Auth::user()->availablePlanItemsCount();

        return inertia('Control/Broadcasts/Create',
            compact(
                'genres',
                'artists',
                'labels',
                'counties',
                'languages',
                'broadcast_types',
                'platform_types',
                'platforms',
                'all_platforms',
                'platform_download_price',
                'publish_country_types',
                'barcode_type',
                'artistBranches',
                'availablePlanItems',
                'users'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BroadcastStoreRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $data = $request->except([
                'image', 'songs', 'main_artists', 'featuring_artists', 'catalog_number', 'promotion_info', 'platforms',
                'counties', 'hashtags'
            ]);

            if (empty($request->validated()['catalog_number'])) {
                $data['catalog_number'] = CatalogNumberService::make();
            }

            $data['added_by'] = auth()->id();

            $broadcast = Broadcast::create($data);

            self::imageUpload($request, $broadcast);

            self::attachSongs($request, $broadcast);

            self::attachArtistFromBroadcast($broadcast, $request);

            self::publishedCountries($request, $broadcast);

            self::createHashtags($request, $broadcast);

            self::createPromotions($request, $broadcast);

            self::createDownloadPlatforms($request, $broadcast);

            if ($broadcast->status != BroadcastStatusEnum::DRAFT) {
                event(new NewBroadcastEvent($broadcast));
            }

            DB::commit();

            return redirect()->route('dashboard.broadcasts.index')
                ->with([
                    'notification' => __('panel.notification_created',
                        ['model' => __('panel.broadcast.title_singular')])
                ]);

        } catch (Exception $exception) {
            DB::rollBack();
            Log::info('Broadcast exception error: '.$exception->getMessage());
            return redirect()->back()->with(['error' => $exception->getMessage()]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Broadcast $broadcast): \Inertia\Response|ResponseFactory
    {
        abort_if(Gate::denies('broadcast_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $broadcast->load('artists', 'label', 'publishedCountries', 'genre', 'subGenre', 'language', 'label',
            'hashtags', 'downloadPlatforms', 'songs.participants.user.roles', 'addedBy', 'promotions');

        $genres = Genre::pluck('name', 'id');
        $artists = Artist::pluck('name', 'id');
        $labels = Label::pluck('name', 'id');
        $countries = CountryServices::get();
        $languages = Country::pluck('name', 'id');
        $platform_types = PlatformTypeEnum::getTitles();
        $broadcast_types = BroadcastTypeEnum::getTitles();
        $platforms = Platform::get()->groupBy('type')->map(function ($platforms) {
            return $platforms->pluck('name', 'id');
        });
        $publish_country_types = BroadcastPublishedCountryTypeEnum::getTitles();
        $time_zones = TimezoneService::getFromInputFormat();
        $youtube_channel_themes = YoutubeChannelThemeEnum::getTitles();
        $statuses = BroadcastStatusEnum::getTitles();

        return inertia('Control/Broadcasts/Show',
            compact('broadcast', 'genres', 'artists', 'labels', 'countries', 'languages', 'platform_types',
                'broadcast_types', 'platforms', 'publish_country_types', 'time_zones', 'youtube_channel_themes',
                'statuses'));
    }

    public function edit(Broadcast $broadcast): \Inertia\Response|ResponseFactory
    {
        abort_if(Gate::denies('broadcast_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $genres = Genre::pluck('name', 'id');
        $artists = Artist::pluck('name', 'id');
        $labels = Label::pluck('name', 'id');
        $counties = CountryServices::get();
        $languages = LanguageServices::get();
        $users = User::pluck('name', 'id');
        $platform_types = PlatformTypeEnum::getTitles();
        $broadcast_types = BroadcastTypeEnum::getTitles();

        //TODO veritabanına eklenecek settingslere olabilir
        $barcode_type = 1;
        $platforms = Platform::get()->groupBy('type')->map(function ($platforms) {
            return $platforms->pluck('name', 'id');
        });

        $all_platforms = Platform::all();
        $publish_country_types = BroadcastPublishedCountryTypeEnum::getTitles();

        $platform_download_price = Platform::$PLATFORM_DOWNLOAD_PRICE;

        $artistBranches = ArtistBranchServices::getBranchesFromInputFormat();

        $availablePlanItems = Auth::user()->availablePlanItemsCount();
        $broadcast = $broadcast->load('artists', 'mainArtists', 'featuredArtists', 'label', 'publishedCountries',
            'genre', 'subGenre', 'language', 'label', 'hashtags', 'downloadPlatforms', 'songs', 'addedBy',
            'promotions', 'introductions');

        return inertia('Control/Broadcasts/Edit',
            compact(
                'broadcast',
                'genres',
                'artists',
                'labels',
                'counties',
                'languages',
                'broadcast_types',
                'platform_types',
                'platforms',
                'all_platforms',
                'platform_download_price',
                'publish_country_types',
                'barcode_type',
                'artistBranches',
                'availablePlanItems',
                'users'
            )
        );

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BroadcastUpdateRequest $request, Broadcast $broadcast): RedirectResponse
    {

        $data = $request->except([
            'image', 'songs', 'main_artists', 'featuring_artists', 'catalog_number', 'promotion_info', 'platforms',
            'counties', 'hashtags'
        ]);

        $broadcast->update($data);

        self::imageUpload($request, $broadcast);

        self::attachSongs($request, $broadcast);

        self::attachArtistFromBroadcast($broadcast, $request);

        self::publishedCountries($request, $broadcast);

        self::createHashtags($request, $broadcast);

        self::createPromotions($request, $broadcast);

        self::createDownloadPlatforms($request, $broadcast);

        if ($broadcast->status != BroadcastStatusEnum::DRAFT) {
            event(new NewBroadcastEvent($broadcast));
        }

        return to_route('dashboard.broadcasts.index')
            ->with([
                'notification' => __('panel.notification_updated', ['model' => __('panel.broadcast.title_singular')])
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Broadcast $broadcast): RedirectResponse
    {
        abort_if(Gate::denies('broadcast_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $broadcast->delete();

        return to_route('dashboard.roles.index')
            ->with([
                'notification' => __('panel.notification_deleted', ['model' => __('panel.broadcast.title_singular')])
            ]);
    }


    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'required|string|max:255'
        ]);

        $search = $request->input('search');

        $labels = BroadcastServices::search($search);

        return response()->json($labels, Response::HTTP_OK);
    }

    public function export(): BinaryFileResponse
    {
        abort_if(Gate::denies('excel_export_broadcasts'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return Excel::download(new BroadcastExport, 'broadcasts.xlsx');

    }

    public function checkUPC(Request $request): JsonResponse
    {
        abort_if(Gate::denies('broadcast_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $result = iTunesServices::lookup($request->q, $request->type);
        if ($result['status']) {
            if ($result['data']['resultCount'] > 0) {

                return response()->json(['exist' => true]);
            }
        }

        return response()->json(['exist' => false]);
    }

    public function checkISRC(Request $request): JsonResponse
    {
        abort_if(Gate::denies('broadcast_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $result = MusicBrainzServices::lookupISRC($request->isrc);

        if ($result['status']) {
            if (key_exists('isrc', $result['data'])) {

                return response()->json(['status' => false, 'message' => __('panel.broadcast.form.isrc_exist')]);
            } elseif (key_exists('error', $result['data'])) {

                if ($result['data']['error'] == 'Not Found') {

                    return response()->json(['status' => true]);
                } elseif ($result['data']['error'] == 'Invalid isrc.') {

                    return response()->json(['status' => false, 'message' => __('panel.broadcast.form.isrc_format')]);
                } else {
                    return response()->json(['status' => false, 'message' => $result['data']['error']]);
                }
            }
        }

        return response()->json(['exist' => false]); // Could not check
    }

    public function getISRC(Request $request): false|string
    {
        abort_if(Gate::denies('broadcast_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return ISRCServices::make($request->input('type'), $request->has('index') ? $request->input('index') : null);
    }

    public function convertAudio(ConvertAudioRequest $request): RedirectResponse
    {
        $broadcast = Broadcast::find($request->broadcast_id);
        $song = Song::find($request->song_id);

        ConvertAudioJob::dispatch($broadcast, $song, $request->all());

        return redirect()->back()
            ->with([
                'notification' => __('panel.notification_created',
                    ['model' => __('panel.convert_audio.title_singular')])
            ]);
    }

    public function addParticipant(Request $request): RedirectResponse
    {
        $request->validate([
            'broadcast_id' => 'required|integer',
            'song_id' => 'required|integer',
            'user_id' => 'required|integer',
            'tasks' => 'required|array',
            'rate' => 'required|integer',
        ]);

        Participant::create(
            [
                'broadcast_id' => $request->broadcast_id,
                'song_id' => $request->song_id,
                'user_id' => $request->user_id,
                'tasks' => json_encode($request->tasks, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES),
                'rate' => $request->rate,
            ]
        );

        return redirect()->back()
            ->with([
                'notification' => __('panel.notification_created',
                    ['model' => __('panel.participant.title_singular')])
            ]);
    }

    public function deleteParticipants(Request $request): RedirectResponse
    {
        $request->validate(['participant_id' => 'required|integer']);

        Participant::find($request->participant_id)->delete();

        return redirect()->back()
            ->with([
                'notification' => __('panel.notification_deleted',
                    ['model' => __('panel.participant.title_singular')])
            ]);

    }

    /**
     * @param  BroadcastStoreRequest  $request
     * @param $broadcast
     * @return void
     */
    protected static function attachSongs(BroadcastStoreRequest $request, $broadcast): void
    {
        foreach ($request->songs as $song) {

            if (empty($song['isrc']) || $song['isrc'] == '***********') {
                $song['isrc'] = ISRCServices::make($broadcast->type->value);
            }

            Song::find($song['id'])->update($song);

            //Participants
            self::createParticipants($song, $broadcast);

            $broadcast->songs()->attach($song['id']);
        }
    }

    /**
     * @param $broadcast
     * @param  BroadcastStoreRequest  $request
     * @return void
     */
    protected static function attachArtistFromBroadcast($broadcast, BroadcastStoreRequest $request): void
    {
        $broadcast->artists()->detach();
        $broadcast->artists()->attach($request->main_artists, ['is_main' => true]);
        $broadcast->artists()->attach($request->featuring_artists, ['is_main' => false]);
    }

    /**
     * @param  mixed  $song
     * @param $broadcast
     * @return mixed
     */
    protected static function createParticipants(mixed $song, $broadcast): mixed
    {
        if (isset($song['lyrics_writers'])) {

            foreach ($song['lyrics_writers'] as $lyrics_writer) {
                Participant::updateOrCreate(
                    [
                        'user_id' => $lyrics_writer['id'],
                        'broadcast_id' => $broadcast->id,
                        'song_id' => $song['id'],
                    ],
                    [
                        'tasks' => $lyrics_writer['tasks'],
                        'rate' => $lyrics_writer['rate'],
                    ]
                );
            }

        }

        return $song;
    }

    /**
     * @param  BroadcastStoreRequest  $request
     * @param $broadcast
     * @return void
     */
    protected static function publishedCountries(BroadcastStoreRequest $request, $broadcast): void
    {
        if ($request->publish_country_type !== BroadcastPublishedCountryTypeEnum::ALL && is_array($request->counties) && count($request->counties) > 0) {
            $broadcast->publishedCountries()->detach();
            $broadcast->publishedCountries()->attach($request->counties);
        }
    }

    /**
     * @param  BroadcastStoreRequest  $request
     * @param $broadcast
     * @return void
     */
    protected static function createHashtags(BroadcastStoreRequest $request, $broadcast): void
    {
        if (isset($request->hashtags) && is_array($request->hashtags) && count($request->hashtags) > 0) {

            $broadcast->hashtags()->detach();

            foreach ($request->hashtags as $value) {
                $broadcast->hashtags()->create([
                    'name' => $value,
                    'code' => Str::slug($value),
                ]);
            }
        }
    }

    /**
     * @param  BroadcastStoreRequest  $request
     * @param $broadcast
     * @return void
     */
    protected static function createPromotions(BroadcastStoreRequest $request, $broadcast): void
    {
        if (isset($request->broadcast_promotion_text) && is_array($request->broadcast_promotion_text) && count($request->broadcast_promotion_text) > 0) {
            foreach ($request->broadcast_promotion_text as $value) {
                $broadcast->introductions()->updateOrCreate(
                    [
                        'language_id' => $value['language_id'],
                    ],
                    [
                        'p_line' => $value['punch_line'],
                        'description' => $value['promotion_text'],
                    ]
                );
            }
        }
    }

    /**
     * @param  BroadcastStoreRequest  $request
     * @param $broadcast
     * @return void
     */
    protected static function createDownloadPlatforms(BroadcastStoreRequest $request, $broadcast): void
    {
        if (isset($request->platforms) && is_array($request->platforms) && count($request->platforms) > 0) {

            $broadcast->downloadPlatforms()->detach();

            foreach ($request->platforms as $platform) {
                $broadcast->downloadPlatforms()->attach(
                    $platform['platformId'],
                    [
                        'price' => $platform['platform_price'],
                        'pre_order_date' => $platform['platform_pre_order_date'],
                        'publish_date' => $platform['publish_date'],
                    ]
                );
            }
        }
    }

    /**
     * @param  BroadcastStoreRequest  $request
     * @param $broadcast
     * @return void
     */
    protected static function imageUpload(BroadcastStoreRequest $request, $broadcast): void
    {
        if ($request->hasFile('image')) {
            BroadcastServices::imageUpload($broadcast, $request->file('image'));
        }
    }
}
