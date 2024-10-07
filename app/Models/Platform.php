<?php

namespace App\Models;

use App\Enums\PlatformTypeEnum;
use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @method static advancedFilter()
 * @method static updateOrCreate(array $array, array $item)
 */
class Platform extends Model implements HasMedia
{
    use HasFactory;
    use HasAdvancedFilter;
    use InteractsWithMedia;

    protected $table = 'platforms';

    protected $fillable = [
        'type',
        'name',
        'code',
        'visible_name',
        'status',
        'description',
        'url',
        'authenticators',
    ];

    protected array $filterable = [
        'type',
        'name',
        'code',
        'visible_name',
        'status',
        'url',
        'authenticators',
    ];

    protected array $orderable = [
        'type',
        'name',
        'code',
        'visible_name',
        'status',
        'url',
        'authenticators',
    ];

    protected $appends = ['image'];

    protected $casts = [
        'type' => PlatformTypeEnum::class,
        'status' => 'boolean',
        'authenticators' => 'array'
    ];

    public static array $PLATFORM_DOWNLOAD_PRICE = [
        0 => 'Free',
        1 => 10.0,
        2 => 20.0,
        3 => 30.0,
        4 => 40.0,
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('platforms')
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
        $file = $this->getMedia('platforms')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->small = $file->getUrl('small');
            $file->thumb = $file->getUrl('thumb');
        }

        return $file;
    }

    public function broadcasts(): BelongsToMany
    {
        return $this->belongsToMany(Broadcast::class, 'broadcast_download_platform', 'platform_id', 'broadcast_id')
            ->withPivot('price', 'pre_order_date', 'release_date');
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'artist_platform', 'platform_id', 'artist_id')
            ->withPivot('url');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
