<?php

namespace App\Models;

use App\Models\Scopes\FilterByUserRoleScope;
use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @method static create(array $array)
 * @method static find(mixed $id)
 */
class Song extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasAdvancedFilter;

    protected $table = 'songs';

    protected $fillable = [
        'name',
        'genre_id',
        'sub_genre_id',
        'type',
        'path',
        'mime_type',
        'size',
        'isrc',
        'is_instrumental',
        'is_explicit',
        'language_id',
        'lyrics',
        'iswc',
        'preview_start',
        'is_cover',
        'remixer_artis',
        'released_before',
        'original_release_date',
        'details',
        'acr_response',
        'added_by',
        'status',
        'status_changed_at',
        'status_changed_by',
        'note'
    ];
    protected array $filterable = [
        'name',
        'isrc',
        'is_instrumental',
        'is_explicit',
        'lyrics',
        'iswc',
        'preview_start',
        'is_cover',
        'remixer_artis',
        'released_before',
        'original_release_date',
    ];
    protected array $orderable = [
        'name',
        'isrc',
        'is_instrumental',
        'is_explicit',
        'lyrics',
        'iswc',
        'preview_start',
        'is_cover',
        'remixer_artis',
        'released_before',
        'original_release_date',
    ];

    protected $casts = [
        'acr_response' => 'array',
        'details' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new FilterByUserRoleScope);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function subGenre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_song', 'song_id', 'product_id');
    }

    public function remixer(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'remixer_artis', 'id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function convertedSong(): HasOne
    {
        return $this->hasOne(ConvertAudio::class, 'song_id', 'id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(EarningReport::class, 'song_id');
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(Earning::class, 'song_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function musixMatch(): HasOne
    {
        return $this->hasOne(MusixMatach::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
