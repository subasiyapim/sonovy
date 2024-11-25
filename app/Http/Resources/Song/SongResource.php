<?php

namespace App\Http\Resources\Song;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
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
            'type' => $this->type->value,
            'status' => $this->products[0]->status->value,
            'name' => $this->name,
            'duration' => $this->duration,
            'artists' => $this->artists,
            'participants' => $this->participants,
            'path' => $this->path,

        ];
    }
}
