<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\Song;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class MusixmatchService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $musixmatch = Integration::where('code', 'musixmatch')->first();

        $this->baseUrl = $musixmatch->url;
        $this->apiKey = $musixmatch->secret;
    }

    public function searchTrackFromIsrc(Song $song)
    {
        if ($song->musixMatch) {
            return $song->musixMatch;
        }

        $response = Http::get("{$this->baseUrl}track.get", [
            'track_isrc' => $song->isrc,
            'apikey' => $this->apiKey
        ])->body();

        if (empty(json_decode($response, true)['message']['body'])) {
            return null;
        }

        self::storeTrack($song, json_decode($response, true));

        return $response;

    }

    public function getLyrics($trackId)
    {
        $response = Http::get("{$this->baseUrl}track.lyrics.get", [
            'track_id' => $trackId,
            'apikey' => $this->apiKey
        ]);

        return json_decode($response->body(), true);
    }

    public function postLyrics(Song $song, $lyrics)
    {
        $response = Http::post("{$this->baseUrl}track.lyrics.post", [
            'commontrack_id' => $song->musixMatch->commontrack_id,
            'apikey' => $this->apiKey,
            'lyrics_body' => $lyrics
        ]);

        return json_decode($response->body(), true);

    }

    public function storeTrack(Song $song, $response)
    {
        $track = $response['message']['body']['track'];

        $song->musixMatch()->updateOrCreate(
            [
                'has_lyrics' => $track['has_lyrics'],
                'has_subtitles' => $track['has_subtitles'],
            ],
            [
                'track_id' => $track['track_id'],
                'track_name' => $track['track_name'],
                'commontrack_id' => $track['commontrack_id'],
                'instrumental' => $track['instrumental'],
                'has_lyrics' => $track['has_lyrics'],
                'has_subtitles' => $track['has_subtitles'],
                'album_id' => $track['album_id'],
                'album_name' => $track['album_name'],
                'artist_id' => $track['artist_id'],
                'artist_name' => $track['artist_name'],
                'track_share_url' => $track['track_share_url'],
                'explicit' => $track['explicit'],
                'updated_time' => Carbon::parse($track['updated_time'])->format('Y-m-d H:i:s'),
            ]);

        if ($track['has_lyrics'] === 1) {
            $lyrics = $this->getLyrics($track['track_id']);

            $lyrics = $lyrics['message']['body']['lyrics'];

            $song->musixMatch()->update([
                'lyrics_id' => $lyrics['lyrics_id'],
                'lyrics_body' => $lyrics['lyrics_body'],
                'lyrics_language' => $lyrics['lyrics_language'] ?? null,
                'script_tracking_url' => $lyrics['script_tracking_url'],
                'pixel_tracking_url' => $lyrics['pixel_tracking_url'],
                'lyrics_copyright' => $lyrics['lyrics_copyright'],
            ]);
        }
    }
}