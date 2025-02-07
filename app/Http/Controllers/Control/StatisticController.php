<?php

namespace App\Http\Controllers\Control;

use Symfony\Component\HttpFoundation\Response;

use App\Enums\ProductStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Statistics\MonthlyStreamsRequest;
use App\Http\Requests\Statistics\PlatformBasedSaleRequest;
use App\Models\Artist;
use App\Models\Product;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Song;
use App\Services\CountryServices;
use App\Services\EarningService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class StatisticController extends Controller
{


    public function index(Request $request)
    {
        $platforms = Platform::all();
        $downloadCounts = [
            "songs" => [
                "count" => 124.000,
                "change" => 5.00,
            ],
            "products" => [
                "count" => 50.000,
                "change" => -3.00,
            ],
            "videos" => [
                "count" => 120.00,
                "change" => 0,
            ],
        ];
        $platformStatistics = [
            "total" => 1253758,
            //Sıra önemlidir
            "platforms" => [
                "spotify" => 20000,
                "apple" => 75000,
                "other" => 5000
            ]

        ];

        //TODO slug'a bakarak songs,products,artists,labels,platforms,countries gelecek


        $slug = $request->query('slug') ?? 'songs';


        $tab = [];
        if ($slug == 'platforms') {
            $tempPlatforms = Platform::all();
            foreach ($tempPlatforms as $index => $value) {
                $tempPlatforms[$index]['amount'] = 1245;
                $tempPlatforms[$index]['percantage'] = 90;
            }
            $tab = $tempPlatforms;
        } else {
            if ($slug == 'songs') {
                $tempSongs = Song::with('mainArtists')->get();

                foreach ($tempSongs as $country) {
                    $country->amount = 1245;
                    $country->percantage = 90;
                }

                $tab = $tempSongs;
            } else {
                if ($slug == 'products') {
                    $tempProducts =
                    $tempPlatforms = Product::with('artists', 'label')
                        ->when($request->input('status'), function ($query) use ($request) {
                            $query->where('status', $request->input('status'));
                        })
                        ->when($request->input('type'), function ($query) use ($request) {
                            $query->where('type', $request->input('type'));
                        })->get();

                    foreach ($tempProducts as $country) {
                        $country->amount = 1245;
                        $country->percantage = 90;
                    }

                    $tab = $tempProducts;
                } else {
                    if ($slug == 'artists') {
                        $tempArtists = Artist::with('platforms', 'products')->get();

                        foreach ($tempArtists as $country) {
                            $country->amount = 1245;
                            $country->percantage = 90;
                        }

                        $tab = $tempArtists;
                    } else {
                        if ($slug == 'labels') {
                            $tempLabels = Label::with('products')->get();

                            foreach ($tempLabels as $country) {
                                $country->amount = 1245;
                                $country->percantage = 90;
                            }

                            $tab = $tempLabels;
                        } else {
                            if ($slug == 'countries') {
                                $tempCountries = CountryServices::get();

                                foreach ($tempCountries as $index => $country) {
                                    $tempCountries[$index]['total_product'] = 12;
                                    $tempCountries[$index]['amount'] = 1245;
                                    $tempCountries[$index]['percantage'] = 90;
                                }

                                $tab = $tempCountries;
                            }
                        }
                    }
                }
            }
        }

        return inertia('Control/Statistics/index', [
            'tab' => $tab,
            'platforms' => $platforms,
            'downloadCounts' => $downloadCounts,
            'platformStatistics' => $platformStatistics,
        ]);
    }

    public function product(Product $product, Request $request)
    {


        $platforms = Platform::all();

        $product->loadMissing(

            'downloadPlatforms',
            'mainArtists',


        );

        $slug = $request->query('slug') ?? 'platforms';


        $tab = [];
        if ($slug == 'platforms') {
            $tempPlatforms = $product->downloadPlatforms()->get();
            foreach ($tempPlatforms as $index => $value) {
                $tempPlatforms[$index]['amount'] = 1245;
                $tempPlatforms[$index]['percantage'] = 90;
            }
            $tab = $tempPlatforms;
        } else {
            if ($slug == 'countries') {
                $tempCountries = $product->publishedCountries()->get(); // Fetch data as a collection

                foreach ($tempCountries as $country) {
                    $country->amount = 1245;
                    $country->percantage = 90;
                }

                $tab = $tempCountries;
            }
        }


        // Bunlar yine gelecek ama product nezdinde
        $downloadCounts = [
            "songs" => [
                "count" => 124.000,
                "change" => 5.00,
            ],
            "products" => [
                "count" => 50.000,
                "change" => -3.00,
            ],
            "videos" => [
                "count" => 120.00,
                "change" => 0,
            ],
        ];
        $platformStatistics = [
            "total" => 1253758,
            //Sıra önemlidir
            "platforms" => [
                "spotify" => 20000,
                "apple" => 75000,
                "other" => 5000
            ]

        ];
        return inertia('Control/Statistics/product', [
            'product' => $product,
            'platforms' => $platforms,
            'downloadCounts' => $downloadCounts,
            'platformStatistics' => $platformStatistics,
            'tab' => $tab,
        ]);
    }

    public function getMonthlyStreams(MonthlyStreamsRequest $request)
    {

        $labels = ['OCA', 'SUB', 'MAR', 'NIS', 'MAY', 'HAZ', 'TEM', 'AGU', 'EYL', 'EKI', 'KAS', 'ARA'];
        $series = [480000, 500000, 250000, 100000, 50000, 750000, 500000, 250000, 100000, 50000, 750000, 500000];
        $total = 350000;
        $percentage = -1.24; // bu - değer de alabilir değişimi numerik olarak represente eder
        return response()->json([
            "labels" => $labels,
            "series" => $series,
            "total" => $total,
            "percentage" => $percentage,
        ], Response::HTTP_OK);
    }

    public function getPlatformBasedSales(PlatformBasedSaleRequest $request)
    {

        $data = [480000, 500000, 250000, 100000, 50000, 750000, 500000, 250000, 100000, 50000, 750000, 500000];
        $total = 120000;
        $percentage = 0.48; // bu - değer de alabilir değişimi numerik olarak represente eder
        return response()->json([
            "data" => $data,
            "total" => $total,
            "percentage" => $percentage,
        ], Response::HTTP_OK);
    }
}
