<?php

namespace App\Http\Resources\Song;

use App\Enums\ProductStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongShowResource extends JsonResource
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
            'image' => $this->products[0]->image,
            'label_name' => $this->products[0]?->label?->name,
            'album_name' => $this->products[0]->album_name,
            'name' => $this->name,
            'product_id' => $this->products[0]->id,
            'version' => $this->version,
            'genre' => $this->genre?->name,
            'sub_genre' => $this->subGenre?->name,
            'type' => $this->type,
            'path' => $this->path,
            'isrc' => $this->isrc,
            'main_artist' => $this->mainArtists()->first(),
            'participants' => $this->participants,
            'platforms' => $this->platforms,
            'other_songs' => $this->otherSongs(),
            'product_status' => ProductStatusEnum::from($this->products[0]->status)->title(),
        ];
    }

    private function getMainArtist()
    {
        $arr = [];

        foreach ($this->products[0]->mainArtists as $artist) {
            $arr['id'] = $artist->id;
            $arr['name'] = $artist->name;
            $arr['image'] = $artist->getFirstMediaUrl('artists', 'thumb');
        }

        return $arr;
    }

    private function otherSongs()
    {
        return $this->products[0]->songs->where('song_id', '!=', $this->id)->flatten()->unique('id')->map(function (
            $song
        ) {
            return [
                'id' => $song->id,
                'name' => $song->name,
                'image' => $song->products[0]->image,
                'album_name' => $song->products[0]->album_name ?? '',
                'duration' => $song->duration,
            ];
        });
    }

    private function getPlatformUrls(string $string)
    {
        $platforms = [];

        foreach ($this->products[0]->platforms as $platform) {
            if ($platform->name === $string) {
                $platforms['id'] = $platform->id;
                $platforms['name'] = $platform->name;
                $platforms['url'] = $platform->pivot->url;
            }
        }

        return $platforms;
    }
}
