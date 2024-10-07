<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static inRandomOrder()
 * @method static firstOrCreate(array $array, array $array1)
 * @method static active()
 */
class Country extends Model
{
    use HasFactory;

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

    //active scope
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'country_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
