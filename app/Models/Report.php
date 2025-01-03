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
        'amount',
        'monthly_amount',
        'user_id',
        'status',
    ];

    protected array $filterable = [
        'period',
        'amount',
        'user_id',
        'status',
    ];

    protected array $orderable = [
        'period',
        'amount',
        'user_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
        'monthly_amount' => 'array',
        'is_auto_report' => 'boolean'
    ];

    protected $appends = ['files'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('tenant_'.tenant('domain').'_income_reports');
    }

    public function getFilesAttribute()
    {
        $file = $this->getMedia('tenant_'.tenant('domain').'_income_reports')->last();
        if ($file) {
            $file->url = $file->getUrl();
        }

        return $file;
    }

}
