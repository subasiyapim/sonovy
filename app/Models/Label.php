<?php

namespace App\Models;

use App\Models\Scopes\FilterByUserRoleScope;
use App\Models\System\Country;
use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

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
        'created_by'
    ];

    protected array $orderable = [
        'id',
        'name',
        'web',
        'email',
        'address'
    ];

    protected array $filterable = [
        'id',
        'name',
        'web',
        'email',
        'address'
    ];

    protected $appends = ['image'];

    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new FilterByUserRoleScope);
        static::creating(fn($model) => self::updateCreatedBy($model));
    }

    protected static function updateCreatedBy($model): void
    {
        $model->setAttribute('created_by', auth()->id() ?? 1);
    }

    public function dspLabels(): HasMany
    {
        return $this->hasMany(DspLabel::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('labels')
            ->singleFile()
            ->registerMediaConversions(function () {
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
        return $this->belongsTo(Country::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
