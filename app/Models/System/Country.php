<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @method static inRandomOrder()
 * @method static firstOrCreate(array $array, array $array1)
 * @method static active()
 */
class Country extends Model
{
    use HasFactory;
    use CentralConnection;

    protected $table = 'countries';

    protected $fillable = [
        'name',
        'ise3',
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
        'region_id',
        'subregion',
        'subregion_id',
        'nationality',
        'timezones',
        'translations',
        'latitude',
        'longitude',
        'emoji',
        'emojiU',
        'is_active',
    ];

    protected $casts = [
        'timezones' => 'array',
        'translations' => 'array',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'country_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
