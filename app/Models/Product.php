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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @method static advancedFilter()
 */
class Product extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'type',
        'copyright_for_publication_image',
        'label_id',
        'right_to_perform_work',
        'has_audiovisual_rights',
        'name',
        'publisher_name',
        'is_for_children',
        'copyright_owner',
        'description',
        'youtube_channel',
        'youtube_channel_theme',
        'genre_id',
        'sub_genre_id',
        'is_compilation_publication',
        'barcode_type',
        'upc_code',
        'ean_code',
        'jan_code',
        'catalog_number',
        'language_id',
        'release_date',
        'original_release_date',
        'remaster_release_date',
        'has_been_released',
        'p_line',
        'publish_country_type',
        'is_publication_date_the_same',
        'publication_date',
        'add_new_to_streaming_platforms',
        'status',
        'correction',
        'added_by',
        'album_type',
        'version',
        'xml_path'
    ];

    protected $casts = [
        'type' => ProductTypeEnum::class,
        'is_for_children' => 'boolean',
        'has_audiovisual_rights' => 'boolean',
        'is_compilation_publication' => 'boolean',
        'has_been_released' => 'boolean',
        'publish_country_type' => ProductPublishedCountryTypeEnum::class,
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


    protected $appends = ['image', 'status_text', 'status_class'];

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
            $file->url = $file->getUrl();
            $file->small = $file->getUrl('small');
            $file->thumb = $file->getUrl('thumb');
        }

        return $file;
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new FilterByUserRoleScope);
    }

    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: fn() => ProductStatusEnum::getTitles()[$this->status]
        );
    }

    protected function statusClass(): Attribute
    {
        return Attribute::make(
            get: function () {
                $styles = [
                    ProductStatusEnum::NEW->value => 'default-badge',
                    ProductStatusEnum::WAITING_FOR_APPROVAL->value => 'dark-badge',
                    ProductStatusEnum::APPROVED->value => 'green-badge',
                    ProductStatusEnum::REJECTED->value => 'red-badge',
                    ProductStatusEnum::NOT_BROADCASTING->value => 'yellow-badge',
                    ProductStatusEnum::DRAFT->value => 'yellow-badge',
                ];

                $statusValue = $this->status ?? 'default';
                $class = $styles[$statusValue] ?? 'default-style';

                return $class;
            }
        );
    }

    public function publishCountryTypeText(): Attribute
    {
        return Attribute::make(
            get: fn() => ProductPublishedCountryTypeEnum::getTitles()[$this->publish_country_type],
        );
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

    public function language(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function hashtags(): MorphMany
    {
        return $this->morphMany(Hashtag::class, 'model');
    }

    public function publishedCountries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'product_published_country', 'product_id', 'country_id');
    }

    public function downloadPlatforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'product_download_platform', 'product_id', 'platform_id')
            ->withPivot('price', 'pre_order_date', 'publish_date');
    }

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'product_song', 'product_id', 'song_id');
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
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

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
