<?php

namespace App\Models;

use App\Enums\ProductPublishedCountryTypeEnum;
use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Models\Scopes\FilterByUserRoleScope;
use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @method static advancedFilter()
 */
class Product extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasAdvancedFilter;

    protected $table = 'products';

    protected $fillable = [
        //common
        'type',
        'album_name',
        'created_by',
        'status',
        'note',
        //Step 1 Music
        'mixed_album',
        'version',
        'genre_id',
        'sub_genre_id',
        'format_id',
        'label_id',
        'p_line',
        'c_line',
        'upc_code',
        'catalog_number',
        'language_id',
        'main_price',

        //step 1 video
        'video_type',
        'description',
        'is_for_kids',

        //step1 ringtone
        'grid_code',

        //step 2
        //step 3
        'production_year',
        'previously_released',
        'previous_release_date',
        'publishing_country_type',
        'physical_release_date'
        //step 4

    ];
    public static array $excludedFields = [
        'id',
        'created_by',
        'updated_at',
        'created_at',
        'deleted_at',
    ];
    protected $casts = [
        'type' => ProductTypeEnum::class,
        'status' => ProductStatusEnum::class,
        'is_for_children' => 'boolean',
        'has_audiovisual_rights' => 'boolean',
        'is_compilation_publication' => 'boolean',
        'has_been_released' => 'boolean',
        'publishing_country_type' => ProductPublishedCountryTypeEnum::class,
        'release_date' => 'date',
    ];

    protected array $filterable = [
        'name',
        'id',
        'type',
        'status'
    ];
    protected array $orderable = [
        'name',
        'id',
        'type',
        'status'
    ];


    protected $appends = ['image'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('products')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(100)
                    ->height(100)
                    ->keepOriginalImageFormat()
                    ->optimize();

                $this->addMediaConversion('small')
                    ->width(400)
                    ->height(400)
                    ->keepOriginalImageFormat()
                    ->optimize();
            });
    }

    public function getImageAttribute()
    {
        $file = $this->getMedia('products')->last();
        if ($file) {
            $file->url = asset($file->getUrl());
            $file->small = asset($file->getUrl('small'));
            $file->thumb = asset($file->getUrl('thumb'));
        }

        return $file;
    }

    protected static function booted(): void
    {
        self::boot();
        self::creating(fn($model) => self::updateCreatedBy($model));
        static::addGlobalScope(new FilterByUserRoleScope);
    }

    protected static function updateCreatedBy($model): void
    {
        $model->creating(['created_by' => auth()->id()]);
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'artist_product', 'product_id', 'artist_id')->withPivot('is_main');
    }

    public function mainArtists(): BelongsToMany
    {
        return $this->artists()->wherePivot('is_main', true);
    }

    public function featuredArtists(): BelongsToMany
    {
        return $this->artists()->wherePivot('is_main', false);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function subGenre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class, 'label_id');
    }

    public function hashtags(): MorphMany
    {
        return $this->morphMany(Hashtag::class, 'model');
    }

    public function downloadPlatforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'product_download_platform', 'product_id', 'platform_id')
            ->withPivot('price', 'pre_order_date', 'publish_date', 'status');
    }

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'product_song')
            ->withPivot('is_favorite');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function introductions(): HasMany
    {
        return $this->hasMany(Introduction::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }


    public function upc(): HasOne
    {
        return $this->hasOne(Upc::class);
    }

    public function histories(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'product_platform_history', 'product_id', 'platform_id')
            ->withPivot('status', 'created_at', 'updated_at');
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function publishedCountries(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\System\Country::class,
            tenant('tenancy_db_name').'.product_published_country',
            'product_id',
            'country_id'
        );
    }
}
