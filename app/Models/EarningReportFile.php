<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Enums\EarningReportFileStatusEnum;
use Illuminate\Support\Facades\Log;

class EarningReportFile extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasAdvancedFilter;

    protected $table = 'earning_report_files';

    protected $fillable = [
        'report_language',
        'user_id',
        'name',
        'is_processed',
        'processed_at',
        'errors',
        'status',
        'total_rows',
        'processed_rows',
        'error_rows'
    ];

    protected array $filterable = [
        'user.name',
        'name',
        'is_processed',
        'processed_at',
        'errors',
        'total_rows',
        'processed_rows',
        'error_rows'
    ];

    protected array $orderable = [
        'user.name',
        'name',
        'is_processed',
        'processed_at',
        'total_rows',
        'processed_rows',
        'error_rows'
    ];

    protected $casts = [
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
        'errors' => 'array',
        'status' => EarningReportFileStatusEnum::class,
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'error_rows' => 'integer'
    ];

    protected $appends = ['file'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('earning_report_files')
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

    public function earningReports(): HasMany
    {
        return $this->hasMany(EarningReport::class, 'earning_report_file_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i');
    }

    public function incrementProcessedRows(): void
    {
        $this->increment('processed_rows');
    }

    public function incrementErrorRows(): void
    {
        $this->increment('error_rows');
    }

    public function addError(array $error): void
    {
        try {
            $currentErrors = $this->errors ?? [];

            // Hata sayısını sınırla (son 1000 hata)
            if (count($currentErrors) >= 1000) {
                array_shift($currentErrors); // En eski hatayı kaldır
            }

            $currentErrors[] = $error;

            $this->update([
                'errors' => $currentErrors,
                'error_rows' => count($currentErrors),
                'status' => EarningReportFileStatusEnum::COMPLETED_WITH_ERRORS->value
            ]);

            Log::info('Hata eklendi', [
                'file_id' => $this->id,
                'error_count' => count($currentErrors),
                'error' => $error
            ]);
        } catch (\Exception $e) {
            Log::error('Hata eklenirken bir sorun oluştu', [
                'file_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
