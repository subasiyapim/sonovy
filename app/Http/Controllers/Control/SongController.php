<?php

namespace App\Http\Controllers\Control;

use App\Enums\ProductStatusEnum;
use App\Enums\SongTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Song\SongChangeStatusRequest;
use App\Http\Requests\Song\SongUpdateRequest;
use App\Http\Resources\Song\SongShowResource;
use App\Models\Participant;
use App\Models\Product;
use App\Services\MusicBrainzServices;
use App\Services\MusixmatchService;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Earning;
use App\Models\Song;
use App\Services\GenreService;
use App\Services\ISRCServices;
use App\Services\LanguageServices;
use App\Services\SongServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use function PHPUnit\Framework\isEmpty;

class SongController extends Controller
{
    protected MusixmatchService $musixmatch;

    public function __construct(MusixmatchService $musixmatch)
    {
        $this->musixmatch = $musixmatch;
    }

    private static function updateArtists(SongUpdateRequest $request, Song $song)
    {
        $main_artists = $request->input('main_artists');
        $featuring_artists = $request->input('featuring_artists');

        $song->artists()->detach();

        $song->artists()->attach($main_artists, ['is_main' => true]);

        if (!empty($featuring_artists)) {
            $song->featuringArtists()->sync($featuring_artists);
        }
    }

    private static function updateLyricsWriters(SongUpdateRequest $request, Song $song)
    {
        $lyrics_writers = $request->input('lyrics_writers');

        if (!empty($lyrics_writers) && is_array($lyrics_writers) && $lyrics_writers[0] !== null && $lyrics_writers[0] !== array()) {
            $song->lyricsWriters()->sync($lyrics_writers);
        }
    }

    private static function updateMusicians(SongUpdateRequest $request, Song $song)
    {

        $musicians = $request->input('musicians');

        if (!empty($musicians)) {
            DB::table('song_musician')->where('song_id', $song->id)->delete();

            foreach ($musicians as $musician) {
                DB::table('song_musician')->insert([
                    'song_id' => $song->id,
                    'musician_id' => $musician['id'],
                    'branch_id' => $musician['role']
                ]);
            }
        }
    }

    private static function updateParticipants(SongUpdateRequest $request, Song $song)
    {
        $participants = $request->input('participants');


        if (!empty($participants)) {
            $song->participants()->delete();

            foreach ($participants as $participant) {
                Participant::create([
                    'product_id' => $request->product_id,
                    'user_id' => $participant['id'],
                    'song_id' => $song->id,
                    'tasks' => $participant['tasks'],
                    'rate' => $participant['rate']
                ]);
            }
        }
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('song_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $songs = Song::with(
            'genre',
            'subGenre',
            'participants',
            'remixer',
            'mainArtists',
            'featuringArtists'
        )
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->whereHas('products')
            ->advancedFilter();


        $types = array_merge([['label' => 'Tümü', 'value' => null]], SongTypeEnum::getTitlesFromInputFormat());

        return inertia('Control/Songs/Index', compact('songs', 'types'));
    }

    public function show(Song $song)
    {
        abort_if(Gate::denies('song_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $song->loadMissing(
            'genre',
            'subGenre',

            'products.label',
            'participants.user',
            'earnings',
            'musixMatch',

            'remixer',
            'convertedSong',
            'reports',
            'mainArtists',
            'featuringArtists',
        );

        if ($song->musixMatch === null) {
            $this->searchTrackFromIsrc($song);
        }

        $isrcResult = MusicBrainzServices::lookupISRC($song->isrc);
        $response = new SongShowResource($song);
        //dd($response->resolve());
        return inertia(
            'Control/Songs/Show',
            [
                'song' => $response->resolve(),
                'isrcResult' => $isrcResult
            ]
        );
    }

    public function searchTrackFromIsrc(Song $song)
    {
        return $this->musixmatch->searchTrackFromIsrc($song);
    }

    public function edit(Song $song)
    {
        abort_if(Gate::denies('song_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $song->loadMissing('genre', 'subGenre', 'language', 'products', 'participants.user');
        $genres = GenreService::getActiveCountriesFromInputFormat();
        $languages = LanguageServices::getActiveLanguagesFromInputFormat();
        $availablePlanItems = Auth::user()->availablePlanItemsCount();


        return inertia('Control/Songs/Edit', compact('song', 'genres', 'languages', 'availablePlanItems'));
    }

    public function destroy(Song $song)
    {
        abort_if(Gate::denies('song_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        //Eğer şarkıya ait yayın varsa silinemez ve inertia ile hata mesajı döndürülür
        if ($song->products()->where('status', ProductStatusEnum::APPROVED->value)->exists()) {
            return redirect()->back()->with(
                [
                    'notification' =>
                        [
                            'type' => 'error',
                            'message' => 'Parçaya ait yayınlar olduğu için silinemez.',
                            'model' => __('control.song.title_singular')
                        ]
                ]
            );
        }

        File::delete(storage_path($song->path));

        $song->delete();

        return redirect()->back();
    }

    public function searchCatalog(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:1|max:255'
        ]);

        $search = $request->input('search');

        $labels = SongServices::searchForCatalog($search);

        return response()->json($labels, Response::HTTP_OK);
    }

// Yinelenen kodu fonksiyon içine alma

    public function uploadSong(Request $request)
    {
        /*dd($request->input('file'));
        $max_file_size = 1024 * 1024 * 1024;
        $allowed_file_types = Setting::where('key', 'allowed_song_formats')->first()->value;
        $validate = Validator::make($request->all(), [
            'type' => ['in:1,2'],
            'file' => ['required', 'mimes:' . $allowed_file_types, 'max:' . $max_file_size],
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }*/
        $response = app('tus-server')->serve();

        return $response;
    }

    public function checkISRC(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);

        $search = $request->input('search');

        $labels = ISRCServices::search($search);

        //        $labels->pluck('isrc')->map(function ($isrc) {
        //            return [
        //                'value' => $isrc,
        //                'label' => $isrc
        //            ];
        //        });
        //
        //        dd($labels);
        return response()->json($labels, Response::HTTP_OK);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);

        $search = $request->input('search');

        $labels = SongServices::search($search);

        return response()->json($labels, Response::HTTP_OK);
    }

    public function changeStatus(SongChangeStatusRequest $request)
    {
        $validated = $request->validated();
        $validated['status_changed_at'] = now();
        $validated['status_changed_by'] = auth()->id();

        $song = Song::find($validated['id']);
        $song->update($validated);

        return redirect()->back()->with([
            'notification' => __('control.notification_updated', ['model' => __('control.song.title_singular')])
        ]);
    }

    public function update(SongUpdateRequest $request, Song $song)
    {
        $excludedFields = $this->getExcludedFields();

        $updateData = Arr::except($request->all(), $excludedFields);

        $song->update($updateData);

        $song->load($this->getRelationsToLoad());

        $this->updateSongDetails($request, $song);


        return redirect()->back()
            ->with([
                'notification' => [
                    'song' => $song,
                    __('control.notification_updated', ['model' => __('control.song.title_singular')]),
                    "message" => "Şarkı başarıyla güncellendi"
                ]
            ]);
    }

    private function getExcludedFields(): array
    {
        return [
            'participants',
            'lyrics_writers',
            'musicians',
            'product_id',
            'featuring_artists',
            'main_artists'
        ];
    }

    private function getRelationsToLoad(): array
    {
        return [
            'artists',
            'mainArtists',
            'featuringArtists',
            'musicians.branch',
            'participants.user',
            'lyricsWriters',
        ];
    }

    private function updateSongDetails(SongUpdateRequest $request, Song $song)
    {
        $updateFunctions = [
            'updateParticipants',
            'updateMusicians',
            'updateLyricsWriters',
            'updateArtists'
        ];

        foreach ($updateFunctions as $function) {
            self::$function($request, $song);
        }
    }

    public function storeLyrics(Request $request, Song $song)
    {
        try {
            // Şarkıya musixMatch ilişkisini yüklüyoruz.
            //$song->loadMissing('musixMatch');

            // Doğrulama işlemi
            $request->validate([
                'lyrics' => 'required|string|min:3|max:10000'
            ]);

            // Şarkı sözlerini alıyoruz
            $lyrics = $request->input('lyrics');

            // MusixMatch API'ye şarkı sözlerini gönderiyoruz
            $this->musixmatch->postLyrics($song, $lyrics);

            // Başarılı geri dönüş
            return redirect()->back()->with([
                'notification' => __('control.notification_updated', ['model' => __('control.song.title_singular')])
            ]);
        } catch (ValidationException $e) {
            // Doğrulama hatalarını frontend'e gönderiyoruz
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            // Diğer tüm hataları bir hata kodu ile birlikte frontend'e gönderiyoruz
            return redirect()->back()->with([
                'notification' => [
                    'type' => 'error',
                    'text' => $e->getMessage(),
                    'model' => __('control.song.title_singular'),
                    'code' => $e->getCode() // Hata kodu gönderimi
                ]
            ])->withInput();
        }
    }

    public function toggleFavorite(Request $request, Song $song)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $product = Product::find($request->product_id);

        $product->songs()->update(['is_favorite' => 0]);

        $product->songs()->updateExistingPivot($song->id, ['is_favorite' => !$request->is_favorite]);

        return redirect()->back();
    }

    public function songsDelete(Request $request)
    {
        $request->validate(
            [
                'ids' => ['array', 'in:'.Song::pluck('id')->implode(',')],
                'product_id' => ['required', 'exists:products,id']
            ]
        );

        $product = Product::find($request->product_id);
        $product->songs()->detach($request->ids);

        return redirect()->back();
    }
}
