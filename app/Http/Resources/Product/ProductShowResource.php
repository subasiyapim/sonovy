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
            'songs' => $this->songs(),
            'regions' => $this->regions(),
            'promotion' => $this->promotion(),
            'distribution' => $this->distribution(),
            'history' => $this->history(),
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
            'id' => $this->id,
            'type' => $this->type,
            'album_name' => $this->album_name,
            'version' => $this->version,
            'genre_id' => $this->genre_id,
            'sub_genre_id' => $this->sub_genre_id,
            'format_id' => $this->format_id,
            'label_id' => $this->label_id,
            'p_line' => $this->p_line,
            'c_line' => $this->c_line,
            'upc_code' => $this->upc_code,
            'catalog_number' => $this->catalog_number,
            'language_id' => $this->language_id,
            'main_price' => $this->main_price,
            'production_year' => $this->production_year,
            'previously_released' => $this->previously_released,
            'previous_release_date' => $this->previous_release_date,
            'publishing_country_type' => $this->publishing_country_type,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image' => $this->image,
            'tab' => self::getTabContent(),
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

    private function songs(): array
    {
        return [

        ];
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
        return AlbumTypeEnum::from($this->format_id)->title();
    }

    private function language()
    {
        $country = Country::find($this->language_id);
        return $country->language ?? $country->name;
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