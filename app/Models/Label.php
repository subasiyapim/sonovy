<?php

namespace App\Models;

use App\Models\Scopes\FilterByUserRoleScope;
use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @method static advancedFilter()
 * @method static create(mixed $validated)
 * @method static inRandomOrder()
 * @method static where(string $string, string $string1, string $string2)
 */
class Label extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;
    use HasAdvancedFilter;

    protected $table = 'labels';

    protected $fillable = [
        'name',
        'country_id',
        'phone',
        'web',
        'email',
        'address',
        'added_by'
    ];

    protected array $orderable = [
        'id',
        'name',
        'phone',
        'web',
        'email',
        'address'
    ];

    protected array $filterable = [
        'id',
        'name',
        'phone',
        'web',
        'email',
        'address'
    ];

    protected $appends = ['image'];

    protected static function booted(): void
    {
        static::addGlobalScope(new FilterByUserRoleScope);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('labels')
            ->singleFile()
            ->useFallbackUrl(asset(''))
            ->useFallbackPath(public_path('/'))
            ->useDisk('tenant_'.tenant('id'))
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
        $file = $this->getMedia('labels')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->small = $file->getUrl('small');
            $file->thumb = $file->getUrl('thumb');
        }

        return $file;
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(\App\Models\System\Country::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
