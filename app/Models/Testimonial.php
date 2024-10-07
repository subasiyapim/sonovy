<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
class Testimonial extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;
    use HasAdvancedFilter;

    protected $table = 'testimonials';

    protected $fillable = [
        'name',
        'role',
        'text',
    ];

    protected array $orderable = [
        'id',
        'name',
        'role',
        'text'
    ];

    protected array $filterable = [
        'id',
        'name',
        'role',
        'text'
    ];

    protected $casts = [
        'text' => 'array',
    ];

    protected $appends = ['avatar'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('testimonials')
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

    public function getAvatarAttribute()
    {
        $file = $this->getMedia('testimonials')->last();
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
