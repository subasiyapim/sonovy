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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\String\s;

/**
 * Class ProductController
 * Controller handling operations related to products.
 */
class ProductController extends Controller
{
    protected int $step = 1;
    protected array $excepted = [];
    protected array $excepted_data = [];

    private static function attachHashtags(Product $product, array $data)
    {
        if (isset($data['hashtags']) && is_array($data['hashtags']) && count($data['hashtags']) > 0) {
            $product->hashtags()->delete();

            foreach ($data['hashtags'] as $value) {
                $product->hashtags()->create([
                    'name' => $value,
                    'code' => Str::slug($value),
                ]);
            }
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('product_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->boolean('generateUpc')) {
            Product::all()->each(function ($product) {
                $product->update(['upc_code' => Str::upper(Str::random(12))]);
            });
        }

        $validator = Validator::make($request->all(), [
            'status' => ['nullable', Rule::enum(ProductStatusEnum::class)],
            'type' => ['nullable', Rule::enum(ProductTypeEnum::class)],
            'period' => ['nullable', 'in:[day,week,month,year]'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('control.catalog.products.index')
                ->withErrors($validator)
                ->withInput();
        }
        if (!$request->query('order') || !$request->query('sort')) {
            return redirect()->route($request->route()->getName(), array_merge($request->query(), [
                'order' => 'asc',
                'sort' => 'status',
            ]));
        }
        $validated = $validator->validated();


        $products = Product::with('artists', 'mainArtists', 'label', 'songs', 'downloadPlatforms')
            ->when($request->input('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->input('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->advancedFilter();
//dd($products[0]->image);
        $statistics = [
            'products' => $this->getProductsGroupedByPeriod($validated['period'] ?? 'month'),
            'labels' => $this->getTopLabelsByProductCount(),
            'artists' => $this->getArtistsAddedLastMonth(),

        ];

        $country_count = Country::count();
        $statuses = ProductStatusEnum::getTitles();
        $types = ProductTypeEnum::getTitles();

        $filters = [
            [
                "title" => "Durum",
                "param" => "status",
                "options" => getDataFromInputFormat($statuses, 'id', 'name', null, true)
            ]
        ];
        return inertia(
            'Control/Products/Index',
            compact('products', 'country_count', 'statuses', 'types', 'statistics', 'filters')
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

        $product->loadMissing(
            'songs.mainArtists',
            'songs.featuringArtists',
            'songs.musicians',
            'songs.participants.user',
            'songs.writers',
            'songs.composers',
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
        $artists = getDataFromInputFormat(Artist::all(), 'id', 'name', 'image');
        $users = getDataFromInputFormat(\App\Models\User::all(), 'id', 'name');
        $statuses = enumToSelectInputFormat(ProductStatusEnum::getTitles());

        return inertia(
            'Control/Products/Show',
            [
                'product' => $response->resolve(),
                'genres' => $genres,
                'artists' => $artists,
                'users' => $users,
                'artistBranches' => $artistBranches,
                'tab' => $tab,
                'statuses' => $statuses
            ]
        );
    }

    public function edit($step, Product $product): \Inertia\Response|ResponseFactory
    {
        abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $genres = getDataFromInputFormat(Genre::pluck('name', 'id'), null, '', null, true);
        $formats = enumToSelectInputFormat(AlbumTypeEnum::getTitles());
        $labels = getDataFromInputFormat(Label::pluck('name', 'id'), 'id', 'name', 'image', true);

        $artists = Artist::with('platforms')->whereNot('id', 1)->get()->map(function ($artist) {
            return [
                'label' => $artist->name,
                'value' => $artist->id,
                'platforms' => $artist->platforms,
            ];
        });

        $users = getDataFromInputFormat(\App\Models\User::all(), 'id', 'name');
        $languages = getDataFromInputFormat(Country::whereNotNull('language')->get(), 'id', 'language', 'emoji');
        $progress = ProductServices::progress($product);
        $platforms = getDataFromInputFormat(Platform::get(), 'id', 'name', 'icon');
        $product_publish_country_types = enumToSelectInputFormat(ProductPublishedCountryTypeEnum::getTitles());
        $main_prices = enumToSelectInputFormat(MainPriceEnum::getTitles());
        $countriesGroupedByRegion = CountryServices::getSelectedCountries(
            $product->id,
            $product->publishing_country_type
        );
        $total_song_duration = totalDuration($product->songs);
        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'id', 'name', 'emoji');
        $video_types = [
            ["label" => "Müzik Video", "value" => 2],
            ["label" => "Apple Video", "value" => 4],
        ];
        $artistBranches = getDataFromInputFormat(ArtistBranch::all(), 'id', 'name');

        $completedSteps = ProductServices::stepCompletedStatus($product);

        $product->load(
            'songs',
            'label',
            'genre',
            'subGenre',
            'label',
            'hashtags',
            'downloadPlatforms',
            'promotions',
            'mainArtists.platforms',
            'featuredArtists.platforms',
            'songs.artists.platforms',
            'songs.mainArtists.platforms',
            'songs.featuringArtists.platforms',
            'songs.composers',
            'songs.musicians',
            'songs.writers',
            'songs.participants.user',
            'media',
        );

        $props = [
            "product" => $product,
            "step" => $step,
            'progress' => $progress,
            'total_song_duration' => $total_song_duration,
            'completed_steps' => $completedSteps
        ];

        switch ($step) {
            case 1:
                $props['genres'] = $genres;
                $props['labels'] = $labels;
                $props['artists'] = $artists;
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
                $props['artists'] = $artists;
                $props['users'] = $users;
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

        if (isset($data['physical_release_date']) && $data['physical_release_date']) {
            $physical_release_date = Carbon::parse($request->physical_release_date)->format('Y-m-d');
            $product->update(['physical_release_date' => $physical_release_date]);
        }

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
            3 => ['published_countries', 'platforms', 'physical_release_date'],
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
        $product->update(['status' => ProductStatusEnum::WAITING_FOR_APPROVAL->value]);
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
    public function destroy(Product $product)
    {
        abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->songs()->detach();
        $product->artists()->detach();
        $product->downloadPlatforms()->detach();
        $product->promotions()->delete();
        $product->hashtags()->delete();
        $product->histories()->delete();
        $product->media()->delete();
        $product->participants()->delete();
        $product->publishedCountries()->detach();

        $product->delete();

        return response()->json(
            [
                'success' => true,
                'message' => __('control.notification_deleted', ['model' => __('control.product.title_singular')]),
            ]
        );
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
        $request->validate([
            'status' => ['required', Rule::enum(ProductStatusEnum::class)],
        ]);

        $product->status = $request->status;
        $product->note = $request->note;
        $product->save();

        if ($product->status == ProductStatusEnum::REJECTED->value) {
            $product->songs()->detach();
        }

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

    public function changeType(Product $product, Request $request): false|string
    {

        $product->type = $request->type;
        $product->save();
        return response()->json(['success' => true]); // Could not check
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

                $data = [
                    'price' => isset($platform['price']) ? $platform['price'] : null,
                    'pre_order_date' => isset($platform['pre_order_date']) ? Carbon::parse($platform['pre_order_date'])->format('Y-m-d') : null,
                    'publish_date' => isset($platform['publish_date']) ? Carbon::parse($platform['publish_date'])->format('Y-m-d') : null,
                    'date' => isset($platform['date']) ? Carbon::parse($platform['date'])->format('Y-m-d') : null,
                    'time' => isset($platform['time']) ? $platform['time']['hours'].':'.$platform['time']['minutes'] : null,
                    'hashtags' => isset($platform['hashtags']) ? json_encode($platform['hashtags']) : null,
                    // Ensure it's a valid JSON
                    'description' => isset($platform['description']) ? $platform['description'] : null,
                    'content_id' => isset($platform['content_id']) ? $platform['content_id'] : null,
                    'privacy' => isset($platform['privacy']) ? $platform['privacy'] : null,
                ];

                $product->downloadPlatforms()->attach($platform['id'], $data);
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

    public function getProductsGroupedByPeriod($period)
    {
        $cacheKey = "products_grouped_by_{$period}";
        $cacheTime = match ($period) {
            'day' => 24 * 60,
            'week' => 7 * 24 * 60,
            'year' => 365 * 24 * 60,
            default => 12 * 60,
        };

        return Cache::remember($cacheKey, $cacheTime, function () use ($period) {
            $startDate = match ($period) {
                'day' => Carbon::now()->subDays(6),
                'week' => Carbon::now()->subWeeks(6),
                'year' => Carbon::now()->subYears(6),
                default => Carbon::now()->subMonths(6),
            };

            $products = Product::where('created_at', '>=', $startDate)
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as count")
                ->groupBy('period')
                ->get();

            $totalCount = $products->sum('count');

            $result = $products->map(function ($item) {
                return [
                    'label' => $item->period,
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
                    'name' => $label->name,
                    'label' => Str::limit($label->name, 3),
                    'value' => $label->products_count,
                ];
            });

            return $result->toArray();
        });
    }


    public function getArtistsAddedLastMonth()
    {
        $startOfLastMonth = Carbon::now()->startOfMonth();
        $endOfLastMonth = Carbon::now()->endOfMonth();

        $artists = Artist::with('products')
            ->whereHas('products', function ($query) use ($startOfLastMonth, $endOfLastMonth) {
                $query->where('status', ProductStatusEnum::APPROVED->value)
                    ->whereBetween('products.created_at', [$startOfLastMonth, $endOfLastMonth]);
            })
            ->distinct()
            ->get();

        $result = $artists->map(function ($artist) {
            return [
                'id' => $artist->id,
                'name' => $artist->name,
                'image' => $artist->image
            ];
        });

        // dd($result);
        return $result->toArray();
    }
}
