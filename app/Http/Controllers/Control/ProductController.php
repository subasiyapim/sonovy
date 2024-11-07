<?php

namespace App\Http\Controllers\Control;

use App\Enums\AlbumTypeEnum;
use App\Enums\MainPriceEnum;
use App\Enums\ProductPublishedCountryTypeEnum;
use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\YoutubeChannelThemeEnum;
use App\Enums\PlatformTypeEnum;
use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Requests\Product\ConvertAudioRequest;
use App\Jobs\ConvertAudioJob;
use App\Models\Artist;
use App\Models\ArtistBranch;
use App\Models\Product;
use App\Models\System\Country;
use App\Models\Genre;
use App\Models\Label;
use App\Models\Participant;
use App\Models\Platform;
use App\Services\ISRCServices;
use App\Services\iTunesServices;
use App\Services\MusicBrainzServices;
use App\Services\TimezoneService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\ProductServices;
use App\Services\CountryServices;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    protected int $step = 1;
    protected array $excepted = [];
    protected array $excepted_data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response|ResponseFactory
    {

        abort_if(Gate::denies('product_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        // $products = Product::with('artists', 'label', 'publishedCountries', 'songs')
        //     ->when($request->input('status'), function ($query) use ($request) {
        //         $query->where('status', $request->input('status'));
        //     })
        //     ->when($request->input('type'), function ($query) use ($request) {
        //         $query->where('type', $request->input('type'));
        //     })
        //     ->advancedFilter();

        $products = Product::with('artists', 'label', 'songs')
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

        $album_types = enumToSelectInputFormat(ProductTypeEnum::getTitles());

        return inertia('Control/Products/Create', compact('album_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = Auth::id();

        $product = Product::create($validated);

        return redirect()->route('control.catalog.products.form.edit', [1, $product->id])
            ->with([
                'notification' => __('control.notification_created', ['model' => __('control.product.title_singular')])
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): \Inertia\Response|ResponseFactory
    {
        abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->load(

            'songs',
            'label',
            'genre',
            'subGenre',
            'label',
            'hashtags',
            'downloadPlatforms',
            'promotions',
            'mainArtists',
            'featuredArtists',
        );

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
        $countriesGroupedByRegion = CountryServices::getCountriesGroupedByRegion();
        return inertia(
            'Control/Products/Show',
            compact(
                'product',
                'genres',
                'artists',
                'labels',
                'countries',
                'languages',
                'platform_types',
                'product_types',
                'platforms',
                'publish_country_types',
                'time_zones',
                'youtube_channel_themes',
                'statuses',
                'countriesGroupedByRegion'
            )
        );
    }

    public function edit($step, Product $product): \Inertia\Response|ResponseFactory
    {
        abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $genres = getDataFromInputFormat(Genre::pluck('name', 'id'), null, '', null, true);
        $formats = enumToSelectInputFormat(AlbumTypeEnum::getTitles());
        $labels = getDataFromInputFormat(Label::pluck('name', 'id'), 'id', 'name', 'image', true);
        $languages = getDataFromInputFormat(Country::whereNotNull('language')->get(), 'id', 'language', 'emoji');
        $progress = ProductServices::progress($product);
        $platforms = getDataFromInputFormat(Platform::get(), 'id', 'name', 'icon');
        $product_published_country_types = enumToSelectInputFormat(ProductPublishedCountryTypeEnum::getTitles());
        $main_prices = enumToSelectInputFormat(MainPriceEnum::getTitles());
        $countriesGroupedByRegion = CountryServices::getCountriesGroupedByRegion();
        $total_song_duration = $product->totalSongsDuration() ?? 0;

        $artistBranches = getDataFromInputFormat(ArtistBranch::all(), 'id', 'name');

        $product->load(
            'songs',
            'label',
            'genre',
            'subGenre',
            'label',
            'hashtags',
            'downloadPlatforms',
            'promotions',
            'mainArtists',
            'featuredArtists',
        );

        $props = [
            "product" => $product,
            "step" => $step,
            'progress' => $progress,
            'total_song_duration' => $total_song_duration,
        ];

        switch ($step) {
            case 1:
                $props['genres'] = $genres;
                $props['labels'] = $labels;
                $props['languages'] = $languages;
                $props['formats'] = $formats;
                $props['main_prices'] = $main_prices;
                break;
            case 2:
                $props['artistBranches'] = $artistBranches;
                $props['genres'] = $genres;
                break;
            case 3:
                $props['platforms'] = $platforms;
                $props['product_published_country_types'] = $product_published_country_types;
                $props['countriesGroupedByRegion'] = $countriesGroupedByRegion;
                break;
            case 4:
                $props['languages'] = $languages;
                break;
        }

        return inertia('Control/Products/Edit', $props);
    }

    public function stepStore(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        switch ($data['step']) {
            case 1:
                $this->excepted = ['main_artists', 'featuring_artists'];
                $this->excepted_data = Arr::except($data, $this->excepted);
                self::attachArtistFromProduct($product, $data);

                break;
            case 2:
                $this->excepted = ['songs'];
                $this->excepted_data = Arr::except($data, $this->excepted);
                break;
            case 3:
                $this->excepted = ['published_countries', 'platforms'];
                $this->excepted_data = Arr::except($data, $this->excepted);
                self::publishedCountries($product, $data);
                self::createDownloadPlatforms($request, $product);
        }

        $product->update($this->excepted_data);
        $progress = ProductServices::progress($product);

        return redirect()->back()
            ->with([
                'notification' => __(
                    'control.notification_updated',
                    ['model' => __('control.product.title_singular')],
                ),
                'progress' => $progress,
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

    // public function export(): BinaryFileResponse
    // {
    //     abort_if(Gate::denies('excel_export_products'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    //     return Excel::download(new ProductExport, 'products.xlsx');
    // }

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
        // $song = Song::find($request->song_id);
        $song = [];
        ConvertAudioJob::dispatch($product, $song, $request->all());

        return redirect()->back()
            ->with([
                'notification' => __(
                    'control.notification_created',
                    ['model' => __('control.convert_audio.title_singular')]
                )
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

            // Song::find($song['id'])->update($song);

            //Participants
            self::createParticipants($song, $product);

            $product->songs()->attach($song['id']);
        }
    }

    /**
     * @param $product
     * @param  array  $data
     * @return void
     */
    protected static function attachArtistFromProduct($product, array $data): void
    {
        $product->artists()->detach();
        $product->artists()->attach($data['main_artists'], ['is_main' => true]);
        $product->artists()->attach($data['featuring_artists'], ['is_main' => false]);
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
    protected static function publishedCountries($product, $data): void
    {
        if ($data['publish_country_type'] !== ProductPublishedCountryTypeEnum::ALL && is_array($data['published_countries']) && count($data['published_countries']) > 0) {
            $product->publishedCountries()->detach();
            $product->publishedCountries()->attach($data['published_countries']);
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
    protected static function createDownloadPlatforms($product, $data): void
    {
        if (isset($data['platforms']) && is_array($data['platforms']) && count($data['platforms']) > 0) {

            $product->downloadPlatforms()->detach();

            foreach ($data['platforms'] as $platform) {
                $product->downloadPlatforms()->attach(
                    $platform['id'],
                    [
                        'price' => $platform['price'],
                        'pre_order_date' => $platform['pre_order_date'],
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
