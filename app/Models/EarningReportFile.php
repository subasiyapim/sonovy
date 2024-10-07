<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class EarningReportFile extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasAdvancedFilter;

    protected $table = 'earning_report_files';

    protected $fillable = [
        'user_id',
        'name',
        'is_processed',
        'processed_at',
    ];

    protected array $filterable = [
        'user.name',
        'name',
        'is_processed',
        'processed_at',
    ];

    protected array $orderable = [
        'user.name',
        'name',
        'is_processed',
        'processed_at',
    ];

    protected $casts = [
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
    ];

    protected $appends = ['file'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('earning_report_files')
            ->useDisk('earning_report_files')
            ->singleFile();
    }

    public function getFileAttribute()
    {
        $file = $this->getMedia('earning_report_files')->last();
        if ($file) {
            $file->url = $file->getUrl();
        }

        return $file;
    }

    public function reports(): HasMany
    {
        return $this->hasMany(EarningReport::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i');
    }
}
