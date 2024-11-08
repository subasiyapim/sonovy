<?php

namespace App\Http\Resources\Product;

use App\Enums\AlbumTypeEnum;
use App\Enums\ProductPublishedCountryTypeEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\SongTypeEnum;
use App\Models\Platform;
use App\Models\System\Country;
use App\Services\CountryServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

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
        $featured_platforms = Platform::whereIn('id', [2, 5, 8])->get(['id'])->pluck('id')->toArray();

        return [
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y H:i'),
            'song_count' => $this->songs->count().' parça',
            'total_duration' => totalDuration($this->songs, true),
            'main_artist' => $this->mainArtists->first(),
            'featured_platforms' => $this->downloadPlatforms()->whereIn('platform_id', $featured_platforms)->get(),
            'platform_count' => $this->downloadPlatforms->count() > 3 ? $this->downloadPlatforms->count() - 3 : 0,
            $this->tab => self::getTabContent()
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
        $groupedCountries = CountryServices::getCountriesGroupedByRegion();
        $country_type = $this->publishing_country_type;

        foreach ($groupedCountries as $region => $countries) {
            foreach ($countries as $key => $country) {
                $groupedCountries[$region][$key]['selected'] = in_array($country['value'],
                    $this->selectedCountryIds($country_type));
            }
        }
        
        return [
            'published_country_type' => ProductPublishedCountryTypeEnum::from($country_type)->title(),
            'countries' => $groupedCountries
        ];
    }

    private function songs()
    {
        return $this->songs->map(function ($song) {
            return [
                'id' => $song->id,
                'type' => SongTypeEnum::from($song->type)->title(),
                'status' => $song->status,
                'name' => $song->name,
                'isrc' => $song->isrc,
                'duration' => $song->duration,
                'artists' => $song->artists,
                'participants' => $this->whenLoaded('participants', $song->participants),
                'analysis' => $song->analysis,
                'details' => $song->details,
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

    private function selectedCountryIds($country_type): array
    {
        return $country_type === ProductPublishedCountryTypeEnum::ALL->value
            ? Country::all()->pluck('id')->toArray()
            : DB::table('product_published_country')
                ->where('product_id', $this->id)
                ->get()->pluck('country_id')->toArray();
    }


}