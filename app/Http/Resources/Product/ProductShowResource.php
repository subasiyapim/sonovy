<?php

namespace App\Http\Resources\Product;

use App\Enums\AlbumTypeEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\SongTypeEnum;
use App\Models\System\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductShowResource extends JsonResource
{
    protected $tab;

    public function __construct($resource, $tab = 'metadata')
    {
        parent::__construct($resource);
        $this->tab = $tab;
    }

    private function getTabContent(): array
    {
        return match ($this->tab) {
            'metadata' => $this->metadata(),
            'songs' => $this->songs()->toArray(),
            'regions' => $this->regions(),
            'promotion' => $this->promotion(),
            'distribution' => $this->distribution(),
            'history' => $this->history(),
            default => $this->metadata(),
        };
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'created_at' => $this->created_at,
            'song_count' => $this->songs->count(),
            'total_duration' => totalDuration($this->songs, true),
            'main_artist' => $this->mainArtists,
            'platforms' => $this->downloadPlatforms,
            self::getTabContent()
        ];
    }

    private function promotion(): array
    {
        return [
            'elele' => 'elele',
            'sdsdsd' => 'sdsdsd'
        ];
    }

    private function metadata(): array
    {
        return [
            'album_name' => $this->album_name,
            'version' => $this->version,
            'genres' => $this->genres(),
            'sub_genres' => $this->subGenres(),
            'format' => $this->format(),
            'type' => $this->type(),
            'p_line' => $this->p_line,
            'c_line' => $this->c_line,
            'upc_code' => $this->upc_code,
            'catalog_number' => $this->catalog_number,
            'main_price' => $this->main_price,
            'language' => $this->language(),
            'artists' => $this->artists(),
            'participants' => $this->participants(),
        ];
    }

    private function regions(): array
    {
        return [

        ];
    }

    private function songs()
    {
        return $this->songs->map(function ($song) {
            return [
                'id' => $song->id,
                'name' => $song->name,
                'version' => $song->version,
                'type' => SongTypeEnum::from($song->type)->title(),
                'genre' => $song->genre->name,
                'sub_genre' => $song->subGenre->name,
                'is_instrumental' => $song->is_instrumental,
                'language' => $song->language?->language ?? $song->language?->name,
                'lyrics' => $song->lyrics,
                'lyrics_writers' => $song->lyrics_writers,
                'iswc' => $song->iswc,
                'isrc' => $song->isrc,
                'preview_start' => $song->preview_start,
                'is_cover' => $song->is_cover,
                'released_before' => $song->released_before,
                'original_release_date' => $song->original_release_date,
                'details' => $song->details,
                'acr_response' => $song->acr_response,
                'duration' => $song->duration,
                'created_by' => $song->created_by,
                'status' => $song->status,
                'status_changed_at' => $song->status_changed_at,
                'status_changed_by' => $song->status_changed_by,
                'note' => $song->note,
            ];
        });

    }

    private function distribution(): array
    {
        return [

        ];
    }

    private function history(): array
    {
        return [

        ];
    }

    private function genres()
    {
        return $this->songs->map(function ($song) {
            return $song->genre->name;
        })->unique()->values();
    }

    private function subGenres()
    {
        return $this->songs->map(function ($song) {
            return $song->subGenre->name;
        })->unique()->values();
    }

    private function format()
    {
        return $this->format_id ? AlbumTypeEnum::from($this->format_id)->title() : $this->format_id;
    }

    private function language()
    {
        $country = Country::find($this->language_id);


        return $country ? $country->language ?? $country->name : $this->language_id;
    }

    private function artists()
    {
        return $this->songs->map(function ($song) {
            return $song->artists->map(function ($artist) {
                return [
                    'id' => $artist->id,
                    'name' => $artist->name,
                    'is_main' => $artist->pivot->is_main,
                ];
            });
        })->flatten()->unique()->values();
    }


    private function participants()
    {
        return $this->songs->map(function ($song) {
            return $song->participants->map(function ($participant) {
                return [
                    'id' => $participant->user_id,
                    'name' => $participant->user->name,
                    'tasks' => $participant->tasks,
                    'rate' => $participant->rate,
                ];
            });
        })->flatten()->unique()->values();
    }

    private function type()
    {
        return ProductTypeEnum::from($this->type->value)->title();
    }


}