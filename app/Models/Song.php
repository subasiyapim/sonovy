<?php

namespace App\Models;

use App\Enums\SongTypeEnum;
use App\Models\System\Country;
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
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @method static create(array $array)
 * @method static find(mixed $id)
 */
class Song extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasAdvancedFilter;
    use LogsActivity;

    public const REQUIRED_FIELDS = [
        'name',
        'genre_id',
        'main_artists',
        'sub_genre_id',
        'lyrics_writers',
    ];
    protected $table = 'songs';
    protected $fillable = [
        'name',
        'version',
        'genre_id',
        'sub_genre_id',
        'type',
        'path',
        'mime_type',
        'size',
        'isrc',
        'is_instrumental',
        'language_id',
        'lyrics',
        'lyrics_writers',
        'iswc',
        'preview_start',
        'is_cover',
        'released_before',
        'original_release_date',
        'details',
        'acr_response',
        'created_by',
        'status',
        'status_changed_at',
        'status_changed_by',
        'note',
        'duration',
        'is_completed'
    ];
    protected array $filterable = [
        'name',
        'isrc',
        'iswc',
        'artists.name'
    ];
    protected array $orderable = [
        'name',
        'isrc',
        'iswc',
        'artists.name'
    ];
    protected $casts = [
        'acr_response' => 'array',
        'details' => 'array',
        'preview_start' => 'array',
        'name' => 'string',
        'type' => SongTypeEnum::class,
    ];

    protected static function booted(): void
    {
        parent::boot();

//        static::updated(function ($song) {
//            self::updateIsCompleted($song);
//        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->useLogName('song');
    }

    public function path(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return asset('storage/tenant_'.tenant('domain').'_songs/'.$value);
            },
        );

    }

    //Deprecated
    protected static function saveWithoutEvents(Song $song): void
    {
        Song::withoutEvents(function () use ($song) {
            $song->save();
        });
    }


    public function mainArtists()
    {
        return $this->belongsToMany(Artist::class, 'artist_song', 'song_id', 'artist_id')
            ->wherePivot('is_main', 1);
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
        return $this->belongsToMany(Product::class, 'product_song', 'song_id', 'product_id')
            ->withPivot('is_favorite');
    }

    public function remixer(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'remixer_artis', 'id');
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'artist_song', 'song_id', 'artist_id')
            ->withPivot('is_main');
    }

    public function featuringArtists()
    {
        return $this->belongsToMany(Artist::class, 'artist_song', 'song_id', 'artist_id')
            ->wherePivot('is_main', 0);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(ArtistBranch::class, 'branch_id', 'id');
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function musixMatch(): HasOne
    {
        return $this->hasOne(MusixMatach::class);
    }

    public function writers(): HasMany
    {
        return $this->hasMany(SongWriter::class);
    }

    public function composers(): HasMany
    {
        return $this->hasMany(SongComposer::class);
    }

    public function musicians(): HasMany
    {
        return $this->hasMany(SongMusician::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
