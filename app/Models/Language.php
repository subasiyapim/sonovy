<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static firstOrCreate(array $array, array $array1)
 * @method static whereAny(string[] $array, string $string, string $string1)
 */
class Language extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $table = 'languages';

    protected $fillable = [
        'name',
        'iso2',
        'iso3',
        'numeric_code',
        'phone_code',
        'native',
        'nationality',
        'timezones',
        'translations',
        'latitude',
        'longitude',
        'emoji',
        'emojiU'
    ];

    protected $casts = [
        'timezones' => 'array',
        'translations' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('panel.datetime_format'));
    }
}
