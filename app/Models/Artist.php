<?php

namespace App\Models;

use App\Models\Scopes\FilterByUserRoleScope;
use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @method static create(array $array)
 * @method static where(string $string, string $string1, string $string2)
 */
class Artist extends Model implements HasMedia
{
    use HasFactory;
    use HasAdvancedFilter;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $table = 'artists';

    protected $fillable = [
        'user_id',
        'name',
        'about',
        'country_id',
        'phone',
        'ipi_code',
        'isni_code',
        'website',
        'created_by'
    ];

    protected array $filterable = [
        'name',
        'ipi_code',
        'isni_code',
    ];

    protected array $orderable = [
        'name',
        'ipi_code',
        'isni_code',
    ];

    protected $appends = ['image'];


    /**
     * @return void
     */
    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new FilterByUserRoleScope);
        static::creating(fn($model) => self::updateCreatedBy($model));
    }

    /**
     * @param $model
     */
    protected static function updateCreatedBy($model): void
    {
        $model->setAttribute(['created_by' => auth()->id()]);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('artists')
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
        $file = $this->getMedia('artists')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->small = $file->getUrl('small');
            $file->thumb = $file->getUrl('thumb');
        }

        return $file;
    }

    public function artistBranches(): BelongsToMany
    {
        return $this->belongsToMany(ArtistBranch::class, 'artist_artist_branch', 'artist_id', 'artist_branch_id');
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'artist_platform', 'artist_id', 'platform_id')
            ->withPivot('url');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(\App\Models\System\Country::class, 'country_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'artist_product', 'artist_id', 'product_id')
            ->withPivot(['is_main']);
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

}
