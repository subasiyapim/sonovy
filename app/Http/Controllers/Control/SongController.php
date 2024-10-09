<?php

namespace App\Http\Controllers\Control;

use App\Enums\ProductStatusEnum;
use App\Enums\SongTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Song\SongChangeStatusRequest;
use App\Http\Requests\Song\SongUpdateRequest;
use App\Services\MusicBrainzServices;
use App\Services\MusixmatchService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Earning;
use App\Models\Song;
use App\Services\GenreService;
use App\Services\ISRCServices;
use App\Services\LanguageServices;
use App\Services\SongServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use function PHPUnit\Framework\isEmpty;

class SongController extends Controller
{
    protected $musixmatch;

    public function __construct(MusixmatchService $musixmatch)
    {
        $this->musixmatch = $musixmatch;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('song_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $songs = Song::with('genre', 'subGenre', 'language', 'broadcasts.artists', 'participants', 'remixer')
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->whereHas('broadcasts')
            ->advancedFilter();

        $types = array_merge([['label' => 'Tümü', 'value' => null]], SongTypeEnum::getTitlesFromInputFormat());

        return inertia('Control/Songs/Index', compact('songs', 'types'));
    }

    public function show(Song $song)
    {
        abort_if(Gate::denies('song_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $song->loadMissing('genre', 'subGenre', 'language', 'broadcasts', 'participants.user', 'earnings', 'musixMatch',
            'addedBy', 'remixer', 'convertedSong', 'reports');

        $earnings = $song->earnings()->advancedFilter();
        $total_earning = Earning::where('song_id', $song->id)->sum('earning');

        if ($song->musixMatch === null) {
            $this->searchTrackFromIsrc($song);
        }

        $isrcResult = MusicBrainzServices::lookupISRC($song->isrc);

        return inertia('Control/Songs/Show', compact('song', 'earnings', 'total_earning', 'isrcResult'));
    }

    public function edit(Song $song)
    {
        abort_if(Gate::denies('song_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $song->loadMissing('genre', 'subGenre', 'language', 'broadcasts', 'participants.user');
        $genres = GenreService::getActiveCountriesFromInputFormat();
        $languages = LanguageServices::getActiveLanguagesFromInputFormat();
        $availablePlanItems = Auth::user()->availablePlanItemsCount();


        return inertia('Control/Songs/Edit', compact('song', 'genres', 'languages', 'availablePlanItems'));
    }

    public function update(SongUpdateRequest $request, Song $song)
    {
        $song->update($request->validated());


        return to_route('dashboard.songs.index')
            ->with(['notification' => __('panel.notification_updated', ['model' => __('panel.song.title_singular')])]);
    }

    public function destroy(Song $song)
    {
        abort_if(Gate::denies('song_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        //Eğer şarkıya ait yayın varsa silinemez ve inertia ile hata mesajı döndürülür
        if ($song->broadcasts()->where('status', ProductStatusEnum::APPROVED->value)->exists()) {
            return redirect()->back()->with(
                [
                    'notification' =>
                        [
                            'type' => 'error',
                            'message' => 'Parçaya ait yayınlar olduğu için silinemez.',
                            'model' => __('panel.song.title_singular')
                        ]
                ]);
        }

        File::delete(storage_path($song->path));

        $song->delete();

        return redirect()->back();

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

    public function searchCatalog(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:1|max:255'
        ]);

        $search = $request->input('search');

        $labels = SongServices::searchForCatalog($search);

        return response()->json($labels, Response::HTTP_OK);
    }

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

    public function changeStatus(SongChangeStatusRequest $request)
    {
        $validated = $request->validated();
        $validated['status_changed_at'] = now();
        $validated['status_changed_by'] = auth()->id();

        $song = Song::find($validated['id']);
        $song->update($validated);

        return redirect()->back()->with([
            'notification' => __('panel.notification_updated', ['model' => __('panel.song.title_singular')])
        ]);
    }

    public function searchTrackFromIsrc(Song $song)
    {
        return $this->musixmatch->searchTrackFromIsrc($song);
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
                'notification' => __('panel.notification_updated', ['model' => __('panel.song.title_singular')])
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
                    'model' => __('panel.song.title_singular'),
                    'code' => $e->getCode() // Hata kodu gönderimi
                ]
            ])->withInput();
        }
    }
}
