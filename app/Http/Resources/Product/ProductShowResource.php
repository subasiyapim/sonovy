<?php

namespace App\Http\Resources\Product;

use App\Enums\AlbumTypeEnum;
use App\Enums\PlatformStatusEnum;
use App\Enums\ProductPublishedCountryTypeEnum;
use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\SongTypeEnum;
use App\Models\Platform;
use App\Models\System\Country;
use App\Services\CountryServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

ini_set('memory_limit', '512M');

class ProductShowResource extends JsonResource
{
    protected mixed $tab;

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
            'promotions' => $this->promotions(),
            'distributions' => $this->distribution(),
            'histories' => $this->histories(),
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
            'id' => $this->id,
            'album_name' => $this->album_name,
            'type' => $this->type->value,
            'type_text' => $this->type(),
            'status_text' => $this->status->title(),
            'image' => $this->image,
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y H:i'),
            'song_count' => $this->songs->count().' parça',
            'total_duration' => totalDuration($this->songs, true),
            'main_artists' => $this->mainArtists,
            'featured_platforms' => $this->downloadPlatforms(),
            'platform_count' => $this->downloadPlatforms->count() > 3 ? $this->downloadPlatforms->count() - 3 : 0,
            'status' => $this->status,
            'note' => $this->note,
            'statuses' => enumToSelectInputFormat(PlatformStatusEnum::getTitles()),
            $this->tab => self::getTabContent()
        ];
    }

    private function promotions(): array
    {
        return $this->promotions->map(function ($promotion) {
            return [
                'title' => $promotion->title,
                'description' => $promotion->description,
                'country' => [
                    'name' => $promotion?->language?->name ?? $promotion?->language?->language,
                    'emoji' => $promotion?->language?->emoji
                ],
            ];
        })->toArray();
    }

    private function metadata(): array
    {
        return [
            'album_name' => $this->album_name,
            'version' => $this->version,
            'genres' => $this->genres(),
            'sub_genres' => $this->subGenres(),
            'format' => $this->format(),
            'type' => $this->type->value,
            'type_text' => $this->type(),
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
        $groupedCountries = CountryServices::getSelectedCountries($this->id, $this->publishing_country_type);
        $country_type = $this->publishing_country_type;

        return [
            'publishing_country_type' => ProductPublishedCountryTypeEnum::from($country_type->value ?? 1)->title(),
            'countries' => $groupedCountries
        ];
    }

    private function songs()
    {
        return $this->songs->map(function ($song) {
            return [
                'id' => $song->id,
                'type_text' => $song->type ? SongTypeEnum::from($song->type->value)->title() : null,
                'type' => $song->type,
                'status' => $song->status,
                'name' => $song->name,
                'isrc' => $song->isrc,
                'duration' => $song->duration,
                'size' => $song->size,
                'lyrics' => $song->lyrics,
                'version' => $song->version,
                'genre_id' => $song->genre_id,
                'sub_genre_id' => $song->sub_genre_id,
                'artists' => $song->artists->map(fn($artist) => ['id' => $artist->id, 'name' => $artist->name]),
                'is_instrumental' => $song->is_instrumental,
                'path' => $song->path,
                'participants' => $song->participants->map(fn($participant) => [
                    'id' => $participant->id,
                    'user' => $participant->user ? ['id' => $participant->user->id, 'name' => $participant->user->name] : null,
                    'tasks' => $participant->tasks,
                    'rate' => $participant->rate
                ]),
                'musicians' => $song->musicians->map(fn($musician) => ['id' => $musician->id, 'name' => $musician->name]),
                'writers' => $song->writers->map(fn($writer) => ['id' => $writer->id, 'name' => $writer->name]),
                'composers' => $song->composers->map(fn($composer) => ['id' => $composer->id, 'name' => $composer->name]),
                'main_artists' => $song->mainArtists->map(fn($artist) => ['id' => $artist->id, 'name' => $artist->name]),
                'featuring_artists' => $song->featuringArtists->map(fn($artist) => ['id' => $artist->id, 'name' => $artist->name]),
                'analysis' => $song->acr_response,
                'details' => $song->details,
                'activities' => $song->activities,
            ];
        });
    }

    /**
     *
     */
    private function distribution(): array
    {

        return $this->downloadPlatforms->map(function ($platform) {

            return [
                'id' => $platform->id,
                'name' => $platform->name,
                'icon' => $platform->icon,
                'price' => $platform->pivot->price,
                'pre_order_date' => $platform->pivot->pre_order_date,
                'publish_date' => $platform->pivot->publish_date,
                'status_text' => $platform->status ? PlatformStatusEnum::from($platform->status)->title() : PlatformStatusEnum::DRAFT->title(),
                'histories' => $this->getHistoriesFromPlatformId($platform->id),
                'created_at' => Carbon::parse($platform->created_at)->format('d-m-Y H:i'),
            ];
        })->toArray();
    }

    private function histories(): array
    {
        return $this->getHistoriesFromProductId($this->id);
    }

    private function genres()
    {
        return $this->songs->map(function ($song) {
            if ($song->genre) {
                return $song->genre->name;
            } else {
                return '';
            }
        })->unique()->values()->implode(', ');
    }

    private function subGenres()
    {
        return $this->songs->map(function ($song) {
            if ($song->subGenre) {
                return $song->subGenre->name;
            } else {
                return '';
            }
        })->unique()->values()->implode(', ');
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
                return $artist;
            });
        })->flatten()->unique()->values();
    }


    private function participants()
    {
        return $this->songs->flatMap(function ($song) {
            return $song->participants->map(function ($participant) {
                return [
                    'id' => $participant->id,
                    'user' => $participant->user ? ['id' => $participant->user->id, 'name' => $participant->user->name] : null,
                    'tasks' => $participant->tasks,
                    'rate' => $participant->rate
                ];
            });
        })->unique('id')->values();
    }

    private function type()
    {
        return ProductTypeEnum::from($this->type->value)->title();
    }

    private function getHistoriesFromPlatformId($id): array
    {
        $data = DB::table('product_download_platform')
            ->where('platform_id', $id)
            ->get();

        $histories = [];

        foreach ($data as $item) {
            $histories[] = [
                'price' => $item->price,
                'pre_order_date' => $item->pre_order_date,
                'publish_date' => $item->publish_date,
                'status' => PlatformStatusEnum::from($item->status)->title(),
            ];
        }

        return $histories;
    }

    private function getHistoriesFromProductId(mixed $id): array
    {
        $data = DB::table('product_download_platform')
            ->where('product_id', $id)
            ->get();

        $histories = [];

        foreach ($data as $item) {
            $histories[] = [
                'platform' => Platform::find($item->platform_id),
                'price' => $item->price,
                'pre_order_date' => $item->pre_order_date,
                'publish_date' => $item->publish_date,
                'status' => PlatformStatusEnum::from($item->status)->title(),
            ];
        }

        return $histories;
    }
}
