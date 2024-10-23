<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ArtistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'about' => $this->about,
            'country' => $this->country,
            'platforms' => $this->getPlatformData(),
            'image' => $this->image,
            'ipi_code' => $this->ipi_code,
            'isni_code' => $this->isni_code,
            'phone' => $this->phone,
            'website' => $this->website,
            'status' => $this->products->count() > 0 ? 'Aktif sanatçı' : 'Pasif sanatçı',
            'tracks_count' => $this->products->count().' parça',
            'genres' => $this->getGenres(),
            'genres_count' => $this->getGenres()->count(),
            'artist_branches' => $this->artistBranches,
        ];
    }

    private function getGenres()
    {
        $genres = $this->products->flatMap(function ($product) {
            $names = [];

            if ($product->genre) {
                $names[] = $product->genre->name;
            }

            if ($product->subGenre) {
                $names[] = $product->subGenre->name;
            }

            return $names;
        });

        return $genres->unique()->values();
    }

    private function getPlatformData()
    {
        return $this->platforms->map(function ($platform) {
            return [
                'id' => $platform->id,
                'name' => $platform->name,
                'icon' => $platform->icon,
                'url' => $platform->pivot->url,
            ];
        });
    }
}
