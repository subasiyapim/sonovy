<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Report extends Model implements HasMedia
{
    use HasFactory;
    use HasAdvancedFilter;
    use InteractsWithMedia;

    protected $table = 'reports';

    protected $fillable = [
        'is_auto_report',
        'period',
        'name',
        'amount',
        'monthly_amount',
        'user_id',
        'status',
        'batch_id'
    ];

    protected array $filterable = [
        'period',
        'name',
    ];

    protected array $orderable = [
        'period',
        'name'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
        'monthly_amount' => 'array',
        'is_auto_report' => 'boolean',
        'status' => 'boolean',
    ];

    protected $appends = ['files'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('income_reports');
    }

    public function getFilesAttribute()
    {
        $file = $this->getMedia('income_reports')->last();
        if ($file) {
            $file->url = $file->getUrl();
        }

        return $file;
    }

}
