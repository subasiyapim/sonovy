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
            'data' => $this->collection,
            'id' => $this->id,
            'name' => $this->name,
            'about' => $this->about,
            'country' => $this->country,
            'platforms' => $this->platforms,
            'image' => $this->image,
            'status' => $this->products->count() > 0 ? 'Aktif sanatçı' : 'Pasif sanatçı',
            'tracks_count' => $this->products->count().' parça',
            'artist_branches' => $this->artistBranches->pluck('name')->take(4),
            'artist_branches_count' => $this->artistBranches->count() > 4 ? $this->artistBranches->count() - 4 : 0,
        ];
    }

    private function artistStatus(mixed $id)
    {


    }
}
