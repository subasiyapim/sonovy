<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Country extends Model
{
    use HasFactory;
    use CentralConnection;

    protected $fillable = [
        'name',
        'iso3',
        'iso2',
        'numeric_code',
        'phone_code',
        'capital',
        'currency',
        'currency_name',
        'currency_symbol',
        'tld',
        'native',
        'region',
        'subregion',
        'nationality',
        'timezones',
        'translations',
        'latitude',
        'longitude',
        'emoji',
        'emojiU',
    ];

    protected $casts = [
        'timezones' => 'array',
        'translations' => 'array',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
