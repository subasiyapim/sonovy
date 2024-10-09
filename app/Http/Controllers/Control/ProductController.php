<?php

namespace App\Http\Controllers\Control;

use App\Enums\AlbumTypeEnum;
use App\Enums\ProductPublishedCountryTypeEnum;
use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\YoutubeChannelThemeEnum;
use App\Enums\PlatformTypeEnum;
use App\Events\NewProductEvent;
use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Requests\Product\ConvertAudioRequest;
use App\Http\Resources\Panel\CountryResource;
use App\Jobs\ConvertAudioJob;
use App\Models\Artist;
use App\Models\ArtistBranch;
use App\Models\Product;
use App\Models\ConvertAudio;
use App\Models\System\Country;
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
use App\Services\ProductServices;
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

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response|ResponseFactory
    {

        abort_if(Gate::denies('product_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $products = Product::with('artists', 'label', 'publishedCountries', 'songs')
            ->when($request->input('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->input('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->advancedFilter();
        $country_count = Country::count();
        $statuses = ProductStatusEnum::getTitles();
        $types = ProductTypeEnum::getTitles();

        return inertia('Control/Products/Index', compact('products', 'country_count', 'statuses', 'types'));
    }

    public function create(): \Inertia\Response|ResponseFactory
    {
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $genres = getDataFromInputFormat(Genre::get(['name', 'id']), 'id', 'name');
        $artists = getDataFromInputFormat(Artist::get(['name', 'id']), 'id', 'name');
        $labels = getDataFromInputFormat(Label::get(['name', 'id']), 'id', 'name');
        $counties = getDataFromInputFormat(\App\Models\System\Country::get(['name', 'id']), 'id', 'name', 'emoji');
        $languages = getDataFromInputFormat(\App\Models\System\Country::get(['name', 'id']), 'id', 'name', 'emoji');
        $users = getDataFromInputFormat(User::get(['name', 'id']), 'id', 'name');
        $platform_types = getDataFromInputFormat(Platform::get(['name', 'id', 'type']), 'id', 'name');
        $product_types = getDataFromInputFormat(ProductTypeEnum::getTitles(), 'id', 'name', null, true);
        $platforms = Platform::all()->groupBy('type')->map(function ($platforms) {
            return getDataFromInputFormat($platforms, 'id', 'name', 'image');
        })->toArray();
        $all_platforms = getDataFromInputFormat(Platform::all(['name', 'id']), 'id', 'name', 'image');
        $publish_country_types = getDataFromInputFormat(ProductPublishedCountryTypeEnum::getTitles(), 'id', 'name',
            null, true);
        $platform_download_price = getDataFromInputFormat(Platform::$PLATFORM_DOWNLOAD_PRICE, 'id', 'name', null, true);
        $artistBranches = getDataFromInputFormat(ArtistBranch::get(['name', 'id'], 'id', 'name'));
        $availablePlanItems = Auth::user()->availablePlanItemsCount();

        return inertia('Control/Products/Create',
            compact(
                'genres',
                'artists',
                'labels',
                'counties',
                'languages',
                'product_types',
                'platform_types',
                'platforms',
                'all_platforms',
                'platform_download_price',
                'publish_country_types',
                'artistBranches',
                'availablePlanItems',
                'users'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request): RedirectResponse
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

            $product = Product::create($data);

            self::imageUpload($request, $product);

            self::attachSongs($request, $product);

            self::attachArtistFromProduct($product, $request);

            self::publishedCountries($request, $product);

            self::createHashtags($request, $product);

            self::createPromotions($request, $product);

            self::createDownloadPlatforms($request, $product);

            if ($product->status != ProductStatusEnum::DRAFT) {
                event(new NewProductEvent($product));
            }

            DB::commit();

            return redirect()->route('control.products.index')
                ->with([
                    'notification' => __('control.notification_created',
                        ['model' => __('control.product.title_singular')])
                ]);

        } catch (Exception $exception) {
            DB::rollBack();
            Log::info('Product exception error: '.$exception->getMessage());
            return redirect()->back()->with(['error' => $exception->getMessage()]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): \Inertia\Response|ResponseFactory
    {
        abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->load('artists', 'label', 'publishedCountries', 'genre', 'subGenre', 'language', 'label',
            'hashtags', 'downloadPlatforms', 'songs.participants.user.roles', 'addedBy', 'promotions');

        $genres = Genre::pluck('name', 'id');
        $artists = Artist::pluck('name', 'id');
        $labels = Label::pluck('name', 'id');
        $countries = CountryServices::get();
        $languages = Country::pluck('name', 'id');
        $platform_types = PlatformTypeEnum::getTitles();
        $product_types = ProductTypeEnum::getTitles();
        $platforms = Platform::get()->groupBy('type')->map(function ($platforms) {
            return $platforms->pluck('name', 'id');
        });
        $publish_country_types = ProductPublishedCountryTypeEnum::getTitles();
        $time_zones = TimezoneService::getFromInputFormat();
        $youtube_channel_themes = YoutubeChannelThemeEnum::getTitles();
        $statuses = ProductStatusEnum::getTitles();

        return inertia('Control/Products/Show',
            compact('product', 'genres', 'artists', 'labels', 'countries', 'languages', 'platform_types',
                'product_types', 'platforms', 'publish_country_types', 'time_zones', 'youtube_channel_themes',
                'statuses'));
    }

    public function edit(Product $product): \Inertia\Response|ResponseFactory
    {
        abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $genres = Genre::pluck('name', 'id');
        $artists = Artist::pluck('name', 'id');
        $labels = Label::pluck('name', 'id');
        $counties = CountryServices::get();
        $languages = LanguageServices::get();
        $users = User::pluck('name', 'id');
        $platform_types = PlatformTypeEnum::getTitles();
        $product_types = ProductTypeEnum::getTitles();

        //TODO veritabanına eklenecek settingslere olabilir
        $barcode_type = 1;
        $platforms = Platform::get()->groupBy('type')->map(function ($platforms) {
            return $platforms->pluck('name', 'id');
        });

        $all_platforms = Platform::all();
        $publish_country_types = ProductPublishedCountryTypeEnum::getTitles();

        $platform_download_price = Platform::$PLATFORM_DOWNLOAD_PRICE;

        $artistBranches = ArtistBranchServices::getBranchesFromInputFormat();

        $availablePlanItems = Auth::user()->availablePlanItemsCount();
        $product = $product->load('artists', 'mainArtists', 'featuredArtists', 'label', 'publishedCountries',
            'genre', 'subGenre', 'language', 'label', 'hashtags', 'downloadPlatforms', 'songs', 'addedBy',
            'promotions', 'introductions');

        return inertia('Control/Products/Edit',
            compact(
                'product',
                'genres',
                'artists',
                'labels',
                'counties',
                'languages',
                'product_types',
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
    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {

        $data = $request->except([
            'image', 'songs', 'main_artists', 'featuring_artists', 'catalog_number', 'promotion_info', 'platforms',
            'counties', 'hashtags'
        ]);

        $product->update($data);

        self::imageUpload($request, $product);

        self::attachSongs($request, $product);

        self::attachArtistFromProduct($product, $request);

        self::publishedCountries($request, $product);

        self::createHashtags($request, $product);

        self::createPromotions($request, $product);

        self::createDownloadPlatforms($request, $product);

        if ($product->status != ProductStatusEnum::DRAFT) {
            event(new NewProductEvent($product));
        }

        return to_route('control.products.index')
            ->with([
                'notification' => __('control.notification_updated', ['model' => __('control.product.title_singular')])
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->delete();

        return to_route('control.roles.index')
            ->with([
                'notification' => __('control.notification_deleted', ['model' => __('control.product.title_singular')])
            ]);
    }


    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'required|string|max:255'
        ]);

        $search = $request->input('search');

        $labels = ProductServices::search($search);

        return response()->json($labels, Response::HTTP_OK);
    }

    public function export(): BinaryFileResponse
    {
        abort_if(Gate::denies('excel_export_products'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return Excel::download(new ProductExport, 'products.xlsx');

    }

    public function checkUPC(Request $request): JsonResponse
    {
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $result = MusicBrainzServices::lookupISRC($request->isrc);

        if ($result['status']) {
            if (key_exists('isrc', $result['data'])) {

                return response()->json(['status' => false, 'message' => __('control.product.form.isrc_exist')]);
            } elseif (key_exists('error', $result['data'])) {

                if ($result['data']['error'] == 'Not Found') {

                    return response()->json(['status' => true]);
                } elseif ($result['data']['error'] == 'Invalid isrc.') {

                    return response()->json(['status' => false, 'message' => __('control.product.form.isrc_format')]);
                } else {
                    return response()->json(['status' => false, 'message' => $result['data']['error']]);
                }
            }
        }

        return response()->json(['exist' => false]); // Could not check
    }

    public function getISRC(Request $request): false|string
    {
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return ISRCServices::make($request->input('type'), $request->has('index') ? $request->input('index') : null);
    }

    public function convertAudio(ConvertAudioRequest $request): RedirectResponse
    {
        $product = Product::find($request->product_id);
        $song = Song::find($request->song_id);

        ConvertAudioJob::dispatch($product, $song, $request->all());

        return redirect()->back()
            ->with([
                'notification' => __('control.notification_created',
                    ['model' => __('control.convert_audio.title_singular')])
            ]);
    }

    public function addParticipant(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'song_id' => 'required|integer',
            'user_id' => 'required|integer',
            'tasks' => 'required|array',
            'rate' => 'required|integer',
        ]);

        Participant::create(
            [
                'product_id' => $request->product_id,
                'song_id' => $request->song_id,
                'user_id' => $request->user_id,
                'tasks' => json_encode($request->tasks, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES),
                'rate' => $request->rate,
            ]
        );

        return redirect()->back()
            ->with([
                'notification' => __('control.notification_created',
                    ['model' => __('control.participant.title_singular')])
            ]);
    }

    public function deleteParticipants(Request $request): RedirectResponse
    {
        $request->validate(['participant_id' => 'required|integer']);

        Participant::find($request->participant_id)->delete();

        return redirect()->back()
            ->with([
                'notification' => __('control.notification_deleted',
                    ['model' => __('control.participant.title_singular')])
            ]);

    }

    /**
     * @param  ProductStoreRequest  $request
     * @param $product
     * @return void
     */
    protected static function attachSongs(ProductStoreRequest $request, $product): void
    {
        foreach ($request->songs as $song) {

            if (empty($song['isrc']) || $song['isrc'] == '***********') {
                $song['isrc'] = ISRCServices::make($product->type->value);
            }

            Song::find($song['id'])->update($song);

            //Participants
            self::createParticipants($song, $product);

            $product->songs()->attach($song['id']);
        }
    }

    /**
     * @param $product
     * @param  ProductStoreRequest  $request
     * @return void
     */
    protected static function attachArtistFromProduct($product, ProductStoreRequest $request): void
    {
        $product->artists()->detach();
        $product->artists()->attach($request->main_artists, ['is_main' => true]);
        $product->artists()->attach($request->featuring_artists, ['is_main' => false]);
    }

    /**
     * @param  mixed  $song
     * @param $product
     * @return mixed
     */
    protected static function createParticipants(mixed $song, $product): mixed
    {
        if (isset($song['lyrics_writers'])) {

            foreach ($song['lyrics_writers'] as $lyrics_writer) {
                Participant::updateOrCreate(
                    [
                        'user_id' => $lyrics_writer['id'],
                        'product_id' => $product->id,
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
     * @param  ProductStoreRequest  $request
     * @param $product
     * @return void
     */
    protected static function publishedCountries(ProductStoreRequest $request, $product): void
    {
        if ($request->publish_country_type !== ProductPublishedCountryTypeEnum::ALL && is_array($request->counties) && count($request->counties) > 0) {
            $product->publishedCountries()->detach();
            $product->publishedCountries()->attach($request->counties);
        }
    }

    /**
     * @param  ProductStoreRequest  $request
     * @param $product
     * @return void
     */
    protected static function createHashtags(ProductStoreRequest $request, $product): void
    {
        if (isset($request->hashtags) && is_array($request->hashtags) && count($request->hashtags) > 0) {

            $product->hashtags()->detach();

            foreach ($request->hashtags as $value) {
                $product->hashtags()->create([
                    'name' => $value,
                    'code' => Str::slug($value),
                ]);
            }
        }
    }

    /**
     * @param  ProductStoreRequest  $request
     * @param $product
     * @return void
     */
    protected static function createPromotions(ProductStoreRequest $request, $product): void
    {
        if (isset($request->product_promotion_text) && is_array($request->product_promotion_text) && count($request->product_promotion_text) > 0) {
            foreach ($request->product_promotion_text as $value) {
                $product->introductions()->updateOrCreate(
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
     * @param  ProductStoreRequest  $request
     * @param $product
     * @return void
     */
    protected static function createDownloadPlatforms(ProductStoreRequest $request, $product): void
    {
        if (isset($request->platforms) && is_array($request->platforms) && count($request->platforms) > 0) {

            $product->downloadPlatforms()->detach();

            foreach ($request->platforms as $platform) {
                $product->downloadPlatforms()->attach(
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
     * @param  ProductStoreRequest  $request
     * @param $product
     * @return void
     */
    protected static function imageUpload(ProductStoreRequest $request, $product): void
    {
        if ($request->hasFile('image')) {
            ProductServices::imageUpload($product, $request->file('image'));
        }
    }
}
