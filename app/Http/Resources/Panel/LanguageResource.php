<?php

namespace App\Http\Resources\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
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
            'name' => $this->translations[app()->getLocale()] ?? $this->name,
            'iso2' => $this->iso2,
            'iso3' => $this->iso3,
            'numeric_code' => $this->numeric_code,
            'phone_code' => $this->phone_code,
            'native' => $this->native,
            'nationality' => $this->nationality,
            'timezones' => $this->timezones,
            'translations' => $this->translations,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'emoji' => $this->emoji,
            'emojiU' => $this->emojiU,
            'is_active' => $this->is_active,
        ];
    }
}
