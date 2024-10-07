<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Site extends Model implements HasMedia
{
    use HasFactory;
    use HasAdvancedFilter;
    use InteractsWithMedia;

    protected $fillable = [
        'domain',
        'title',
        'dns',
        'email',
        'copyright_email',
        'phone',
        'has_contact_form',
        'user_id',
        'language',
        'timezone_id',
    ];

    protected array $filterable = [
        'id',
        'domain',
        'title',
        'dns',
        'email',
        'copyright_email',
        'phone',
        'has_contact_form',
        'user.name',
        'language'
    ];

    protected array $orderable = [
        'id',
        'domain',
        'title',
        'dns',
        'email',
        'copyright_email',
        'phone',
        'has_contact_form',
        'user.name',
        'language'
    ];

    protected $appends = ['logo'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('sites')
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
        $file = $this->getMedia('sites')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->small = $file->getUrl('small');
            $file->thumb = $file->getUrl('thumb');
        }

        return $file;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function timezone(): BelongsTo
    {
        return $this->belongsTo(Timezone::class);
    }
}
