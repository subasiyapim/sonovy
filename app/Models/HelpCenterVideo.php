<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HelpCenterVideo extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;
    use HasAdvancedFilter;

    protected $table = 'helpcenter_videos';

    protected $fillable = [
        'title',
    ];

    protected $casts = [
        'title' => 'array',
    ];

    protected array $orderable = [
        'title',
    ];

    protected array $filterable = [
        'title',
    ];

    protected $appends = ['video'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('help_center_videos')
            ->useDisk('help_center_videos')
            ->singleFile();
    }

    public function getVideoAttribute()
    {
        $file = $this->getMedia('help_center_videos')->last();

        return $file;
    }
}
