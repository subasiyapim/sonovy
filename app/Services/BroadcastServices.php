<?php

namespace App\Services;

use App\Models\Broadcast;
use App\Models\Participant;
use App\Models\Song;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BroadcastServices
{
    public static function imageUpload($model, $image): void
    {
        $name = Str::random(10) . '-' . time();
        $file_name = Str::slug($name);
        $collection = 'broadcasts';

        MediaServices::upload($model, $image, $name, $file_name, $collection, $collection);
    }

    /**
     * @throws \Exception
     * @deprecated
     */
    public static function create($data)
    {
        $arr = [];
        $arr['added_by'] = auth()->id();
        $arr['type'] = $data['type'];
        $arr['copyright_for_publication_image'] = $data['copyright_for_publication_image'];
        $arr['label_id'] = $data['label_id'];
        $arr['right_to_perform_work'] = $data['right_to_perform_work'];
        $arr['has_audiovisual_rights'] = $data['has_audiovisual_rights'];
        $arr['name'] = $data['name'] ?? null;
        $arr['publisher_name'] = $data['publisher_name'];
        $arr['is_for_children'] = $data['is_for_children'];
        $arr['copyright_owner'] = $data['copyright_owner'];
        $arr['description'] = isset($data['description']) ?? null;
        $arr['youtube_channel'] = $data['youtube_channel'];
        $arr['youtube_channel_theme'] = isset($data['youtube_channel_theme']) ?? null;
        $arr['genre_id'] = $data['genre_id'];
        $arr['sub_genre_id'] = $data['sub_genre_id'];
        $arr['is_compilation_publication'] = $data['is_compilation_publication'];
        $arr['barcode_type'] = $data['barcode_type'];
        $arr['upc_code'] = isset($data['upc_code']) ?? null;
        $arr['ean_code'] = isset($data['ean_code']) ?? null;
        $arr['jan_code'] = isset($data['jan_code']) ?? null;
        $arr['catalog_number'] = $data['catalog_number'];
        $arr['language_id'] = $data['language_id'];
        $arr['release_date'] = isset($data['release_date']) ? Carbon::parse($data['release_date'])->format('Y-m-d') : null;
        $arr['original_release_date'] = isset($data['original_release_date']) ? Carbon::parse($data['original_release_date'])->format('Y-m-d') : null;
        $arr['has_been_released'] = $data['has_been_released'];
        $arr['publish_country_type'] = $data['publish_country_type'];
        $arr['is_publication_date_the_same'] = $data['is_publication_date_the_same'];
        $arr['publication_date'] = isset($data['publication_date']) ? Carbon::parse($data['publication_date'])->format('Y-m-d') : null;
        $arr['add_new_to_streaming_platforms'] = $data['add_new_to_streaming_platforms'];
        $arr['remaster_release_date'] = isset($data['remaster_release_date']) ? Carbon::parse($data['remaster_release_date'])->format('Y-m-d') : null;
        $arr['p_line'] = $data['p_line'];


        $image = $data['image'] ?? null;
        $main_artists = is_array($data['main_artists']) && count($data['main_artists']) > 0 ? $data['main_artists'] : null;
        $featuring_artists = is_array($data['featuring_artists']) && count($data['featuring_artists']) > 0 ? $data['featuring_artists'] : null;

        $songs = $data['songs'];

        DB::beginTransaction();

        try {
            $broadcast = Broadcast::create($arr);
            $broadcast->artists()->attach($main_artists, ['is_main' => 1]);
            $broadcast->artists()->attach($featuring_artists, ['is_main' => 0]);

            foreach ($songs as $song) {
                Song::find($song['id'])->update($song);

                if (isset($song['lyrics_writers'])) {
                    foreach ($song['lyrics_writers'] as $lyrics_writer) {
                        Participant::create(
                            [
                                'broadcast_id' => $broadcast->id,
                                'song_id' => $song['id'],
                                'artist_id' => $lyrics_writer['id'],
                                'tasks' => $lyrics_writer['tasks'],
                                'rate' => $lyrics_writer['rate'],
                            ]
                        );
                    }
                }

                $broadcast->songs()->attach($song['id']);
            }

            self::imageUpload($broadcast, $image);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $broadcast;

    }

    public static function search($search): mixed
    {
        return Broadcast::with('songs')->where('name', 'like', '%' . $search . '%')->get();
    }
}
