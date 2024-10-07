<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
class Partner extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;
    use HasAdvancedFilter;

    protected $table = 'partners';

    protected $fillable = [
        'name',
    ];

    protected array $orderable = [
        'id',
        'name',
    ];

    protected array $filterable = [
        'id',
        'name',
    ];

    protected $appends = ['logo'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('partners')
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

    public function getLogoAttribute()
    {
        $file = $this->getMedia('partners')->last();
        if ($file) {
            $file->url   = $file->getUrl();
            $file->small = $file->getUrl('small');
            $file->thumb = $file->getUrl('thumb');
        }

        return $file;
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
