<?php

namespace App\Http\Controllers;


use App\Models\AnnouncementTemplate;
use App\Models\Artist;
use App\Models\ArtistBranch;
use App\Models\Product;
use App\Models\System\City;
use App\Models\Feature;
use App\Models\Label;
use App\Models\Service;
use App\Models\Plan;
use App\Models\Song;
use App\Models\System\District;
use App\Models\Title;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PubController extends Controller
{


    public function lastArtist(Request $request)
    {
        $artist = Artist::with('platforms')->latest('id')->first();

        return response()->json($artist, Response::HTTP_OK);
    }


    public function lastLabel(Request $request)
    {
        $artist = Label::latest('id')->first();

        return response()->json($artist, Response::HTTP_OK);
    }

    public function lastSong(Request $request)
    {
        $artist = Song::latest('id')->first();

        return response()->json($artist, Response::HTTP_OK);
    }

    public function findSong(Request $request)
    {
        $queryParams = [
            "id" => $request->id,
        ];

        // Şarkıyı ve ilişkili verileri yükle
        $song = Song::with([
            'mainArtists',
            'featuringArtists',
            'genre',
            'products' => function ($query) {
                $query->withPivot('is_favorite');
            }
        ])->where($queryParams)->orderBy('id', 'desc')->first();

        if ($song) {
            // Tus yüklemelerinden sonra görüntülenmesi için
            // bu bilgileri ekle
            $song->is_completed = true; // Aksiyon butonlarının görünmesi için

            // Eğer ürün pivot verisi varsa, doğrudan erişilebilir hale getir
            if ($song->products && count($song->products) > 0) {
                if (!isset($song->pivot)) {
                    $pivotData = $song->products[0]->pivot->toArray();
                    $song->pivot = (object)$pivotData;
                }

                // Pivot verilerini products koleksiyonundan kaldır (veri duplikasyonunu önlemek için)
                $song->products->transform(function ($product) {
                    $product->setRelation('pivot', null);
                    return $product;
                });
            } else {
                // Ürün yoksa varsayılan pivot verisi oluştur
                $song->pivot = (object)[
                    'is_favorite' => 0
                ];
            }

            // Debugging için log kaydı
            \Illuminate\Support\Facades\Log::info('Song found for TUS upload', [
                'song_id' => $song->id,
                'song_name' => $song->name,
                'has_pivot' => isset($song->pivot) ? 'yes' : 'no',
                'is_completed' => $song->is_completed,
                'product_count' => $song->products ? count($song->products) : 0
            ]);
        } else {
            \Illuminate\Support\Facades\Log::warning('Song not found', [
                'request_id' => $request->id
            ]);
        }

        return response()->json($song, Response::HTTP_OK);
    }

    public function findUser(Request $request)
    {

        $whereData = [
            "id" => $request->id,
        ];
        $song = Song::where($whereData)->orderBy('id', 'desc')->first();

        return response()->json($song, Response::HTTP_OK);
    }

    public function findArtist(Request $request)
    {
        $artist = Artist::with('platforms')->find($request->id);

        return response()->json($artist, Response::HTTP_OK);
    }

    public function findAllArtists(Request $request)
    {

        $ids = $request->ids;


        $artists = Artist::whereIn('id', $ids)->get();

        return response()->json($artists, Response::HTTP_OK);
    }

    public function findLabel(Request $request)
    {
        $id = $request->id;

        $label = Label::where('id', '=', $id)->first();

        return response()->json($label, Response::HTTP_OK);
    }

    public function findBroadcast(Request $request)
    {
        $id = $request->id;

        $label = Product::with('songs')->where('id', '=', $id)->first();

        return response()->json($label, Response::HTTP_OK);
    }

    public function findAnnouncementTemplates(Request $request)
    {
        $id = $request->id;
        $label = AnnouncementTemplate::where('id', '=', $id)->first();

        return response()->json($label, Response::HTTP_OK);
    }

    public function findAllCities(Request $request)
    {
        $country_id = $request->country_id;

        $label = City::where('country_id', '=', $country_id)->get();

        return response()->json($label, Response::HTTP_OK);
    }
    public function findAllDistricts(Request $request)
    {
        $city_id = $request->city_id;

        $districts = District::where('city_id', '=', $city_id)->get();

        return response()->json($districts, Response::HTTP_OK);
    }

    public function findAllStates(Request $request)
    {
        $country_id = $request->country_id;

        // State modeli bulunamadığı için Country modelinden state'leri alıyoruz
        // veya başka bir model kullanıyoruz
        $states = \App\Models\System\Country::find($country_id)->states ?? collect([]);

        return response()->json($states, Response::HTTP_OK);
    }


    public function lastAnnouncementTemplate(Request $request)
    {
        $announcement_template = AnnouncementTemplate::latest('id')->first();

        return response()->json($announcement_template, Response::HTTP_OK);
    }

    public function lastTitle(Request $request)
    {
        $title = Title::latest('id')->first();

        return response()->json($title, Response::HTTP_OK);
    }

    public function lastArtistBranch(Request $request)
    {
        $artistBranch = ArtistBranch::latest('id')->first();

        return response()->json($artistBranch, Response::HTTP_OK);
    }

    public function lastFeature(Request $request)
    {
        $feature = Feature::latest('id')->first();

        return response()->json($feature, Response::HTTP_OK);
    }

    public function lastPlan(Request $request)
    {
        $plan = Plan::latest('id')->first();

        return response()->json($plan, Response::HTTP_OK);
    }

    public function lastService(Request $request)
    {
        $service = Service::latest('id')->first();

        return response()->json($service, Response::HTTP_OK);
    }


    public function findPlan(Request $request)
    {
        $plan = Plan::find($request->id);

        return response()->json($plan, Response::HTTP_OK);
    }
}
