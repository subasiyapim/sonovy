<?php

namespace App\Http\Controllers\Control;

use App\Enums\AlbumTypeEnum;
use App\Enums\BroadcastStatusEnum;
use App\Enums\MainPriceEnum;
use App\Enums\ProductPublishedCountryTypeEnum;
use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\VideoTypeEnum;
use App\Events\NewBroadcastEvent;
use App\Events\NewProductEvent;
use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Requests\Product\ConvertAudioRequest;
use App\Http\Resources\Product\ProductShowResource;
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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\ProductServices;
use App\Services\CountryServices;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProductController
 * Controller handling operations related to products.
 */
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

        $products = Product::with('artists', 'mainArtists', 'label', 'songs', 'downloadPlatforms')
            ->when($request->input('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->input('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->advancedFilter();

        $statistics = [
            'products' => $this->getProductsGroupedByMonth(),
            'labels' => $this->getTopLabelsByProductCount(),
            'artists' => $this->getArtistsAddedLastMonth(),
        ];

        $country_count = Country::count();
        $statuses = ProductStatusEnum::getTitles();
        $types = ProductTypeEnum::getTitles();

        return inertia(
            'Control/Products/Index',
            compact('products', 'country_count', 'statuses', 'types', 'statistics')
        );
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
            'songs.mainArtists',
            'songs.featuringArtists',
            'songs.musicians.branch',
            'songs.participants.user',
            'songs.lyricsWriters',
            'label',
            'genre',
            'subGenre',
            'label',
            'hashtags',
            'downloadPlatforms.histories',
            'promotions',
            'mainArtists',
            'featuredArtists',
            'histories',

        );

        $tab = 'metadata';
        $tab = request()->input('slug') ?? $tab;

        $response = new ProductShowResource($product, $tab);
        $genres = getDataFromInputFormat(Genre::pluck('name', 'id'), null, '', null, true);
        $artistBranches = getDataFromInputFormat(ArtistBranch::all(), 'id', 'name');

        return inertia(
            'Control/Products/Show',
            [
                'product' => $response->resolve(),
                'genres' => $genres,
                'artistBranches' => $artistBranches,
                'tab' => $tab,
            ]
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
        $product_publish_country_types = enumToSelectInputFormat(ProductPublishedCountryTypeEnum::getTitles());
        $main_prices = enumToSelectInputFormat(MainPriceEnum::getTitles());
        $countriesGroupedByRegion = CountryServices::getSelectedCountries(
            $product->id,
            $product->publishing_country_type);
        $total_song_duration = totalDuration($product->songs);
        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'id', 'name', 'emoji');
        $video_types = enumToSelectInputFormat(VideoTypeEnum::getTitles());
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
            'songs.artists',
            'songs.mainArtists',
            'songs.featuringArtists',
            'songs.musicians.branch',
            'songs.participants.user',
            'songs.lyricsWriters',
            'media'
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
                $props['countries'] = $countries;
                $props['artistBranches'] = $artistBranches;
                $props['video_types'] = $video_types;
                $props['platforms'] = $platforms;
                break;
            case 2:
                $props['artistBranches'] = $artistBranches;
                $props['genres'] = $genres;
                $props['countries'] = $countries;
                break;
            case 3:
                $props['platforms'] = $platforms;
                $props['product_publish_country_types'] = $product_publish_country_types;
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
        $step = $data['step'];
        $this->excepted_data = $this->getExceptedData($data, $step);

        switch ($step) {
            case 1:
                $this->handleStepOne($product, $data);
                break;
            case 2:
                $this->handleStepTwo($product, $data);
                break;
            case 3:
                $this->handleStepThree($product, $data);
                break;
            case 4:
                $this->handleStepFour($product, $data);
                break;
        }

        $product->update($this->excepted_data);

        $progress = ProductServices::progress($product);

        return $this->redirectWithNotification($progress);
    }

    private function getExceptedData(array $data, int $step): array
    {
        $exceptedKeys = match ($step) {
            1 => ['main_artists', 'featuring_artists'],
            2 => ['songs'],
            3 => ['published_countries', 'platforms'],
            default => []
        };

        return Arr::except($data, $exceptedKeys);
    }

    private function handleStepOne(Product $product, array $data): void
    {
        self::attachArtistFromProduct($product, $data);
    }

    private function handleStepTwo(Product $product, array $data): void
    {
        // Step 2 specific logic (if any)
    }

    private function handleStepThree(Product $product, array $data): void
    {
        self::publishedCountries($product, $data);
        self::createDownloadPlatforms($product, $data);
    }

    private function handleStepFour(Product $product, array $data): void
    {
        self::createPromotions($product, $data);
    }

    private function redirectWithNotification($progress): RedirectResponse
    {
        return redirect()->back()
            ->with([
                'notification' => __('control.notification_updated', ['model' => __('control.product.title_singular')]),
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

    public function changeStatus(Product $product, Request $request): JsonResponse
    {
        $product->status = $request->status;
        $product->note = $request->note;
        $product->save();

        if ($product->status != ProductStatusEnum::DRAFT->value && $product->details == null) {

            event(new NewProductEvent($product));
        }

        return response()->json(['success' => true]);
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

        if ($data['mixed_album']) {
            $variousArtistID = 1;

            $product->mainArtists()->attach([$variousArtistID], ['is_main' => true]);
        } else {
            $product->artists()->attach($data['main_artists'], ['is_main' => true]);
            $product->artists()->attach($data['featuring_artists'], ['is_main' => false]);
        }
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
        DB::table('product_published_country')->where('product_id', $product->id)->delete();

        foreach ($data['published_countries'] as $country) {
            DB::table('product_published_country')->insert([
                'product_id' => $product->id,
                'country_id' => $country,
            ]);
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
    protected static function createPromotions($product, $data): void
    {
        if (isset($data['promotions']) && is_array($data['promotions']) && count($data['promotions']) > 0) {

            $product->promotions()->delete();

            foreach ($data['promotions'] as $promotion) {

                $product->promotions()->create($promotion);
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
                        'pre_order_date' => !empty($platform['pre_order_date']) ? $platform['pre_order_date'] : null,
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

    public function getProductsGroupedByMonth()
    {
        return Cache::remember('products_grouped_by_month', 12 * 60, function () {
            $months = [];
            for ($i = 1; $i <= 12; $i++) {
                $months[$i] = Carbon::createFromDate(null, $i, 1)->locale(App::currentLocale())->isoFormat('MMMM');
            }

            $productsByMonth = Product::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->get();

            $totalCount = $productsByMonth->sum('count');

            $result = $productsByMonth->map(function ($item) use ($months) {
                return [
                    'label' => $months[$item->month],
                    'value' => $item->count,
                ];
            });

            return [
                'total' => $totalCount,
                'data' => $result->toArray(),
            ];
        });
    }

    private function getTopLabelsByProductCount()
    {
        return Cache::remember('top_labels_by_product_count', 12 * 60, function () {
            $labels = Label::with('products')
                ->whereHas('products')
                ->withCount('products')
                ->orderBy('products_count', 'desc')
                ->take(3)
                ->get(['name']);

            $result = $labels->map(function ($label) {
                return [
                    'label' => Str::limit($label->name, 3),
                    'value' => $label->products_count,
                ];
            });

            return $result->toArray();
        });
    }


    public function getArtistsAddedLastMonth()
    {
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        $artists = Artist::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->get();

        $result = $artists->map(function ($artist) {
            return [
                'id' => $artist->id,
                'name' => $artist->name,
                'image' => $artist->image
            ];
        });

        return $result->toArray();
    }
}
