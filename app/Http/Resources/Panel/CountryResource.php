<?php

namespace App\Http\Resources\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'language' => $this->language ?? $this->name,
            'numeric_code' => $this->numeric_code,
            'phone_code' => $this->phone_code,
            'capital' => $this->capital,
            'currency' => $this->currency,
            'currency_name' => $this->currency_name,
            'currency_symbol' => $this->currency_symbol,
            'tld' => $this->tld,
            'native' => $this->native,
            'region' => $this->region,
            'region_id' => $this->region_id,
            'subregion' => $this->subregion,
            'subregion_id' => $this->subregion_id,
            'nationality' => $this->nationality,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'emoji' => $this->emoji,
            'emojiU' => $this->emojiU,
            'is_active' => $this->is_active,
            'timezones' => $this->timezones,
            'translations' => $this->translations,
        ];
    }
}
