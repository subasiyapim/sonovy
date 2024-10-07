<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HelpCenterArticle extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;
    use HasAdvancedFilter;

    protected $table = 'articles';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
    ];

    protected $casts = [
        'title' => 'array',
        'subtitle' => 'array',
        'description' => 'array',
    ];

    protected array $orderable = [
        'title',
        'subtitle',
    ];

    protected array $filterable = [
        'title',
        'subtitle',
        'description',
        'question',
        'answer',
    ];

    protected $appends = ['image'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('articles')
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
        $file = $this->getMedia('articles')->last();
        if ($file) {
            $file->url   = $file->getUrl();
            $file->small = $file->getUrl('small');
            $file->thumb = $file->getUrl('thumb');
        }

        return $file;
    }
}
