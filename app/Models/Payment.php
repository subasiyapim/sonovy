<?php

namespace App\Models;

use App\Enums\PaymentProcessTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Payment extends Model implements HasMedia
{
    use HasFactory;
    use HasAdvancedFilter;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'process_type',
        'status',
        'amount',
        'account_id',
        'payment_date',
        'payment_type',
        'commission_rate',
        'commission',
    ];

    protected array $filterable = [
        'id',
        'user_id',
        'process_type',
        'status',
        'amount',
        'account_id',
        'payment_date',
        'payment_type',
        'commission_rate',
        'commission',
    ];

    protected array $orderable = [
        'id',
        'user_id',
        'process_type',
        'status',
        'amount',
        'account_id',
        'payment_date',
        'payment_type',
        'commission_rate',
        'commission',
    ];

    protected $casts = [
        'payment_date' => 'datetime:d-m-Y H:i',
        'created_at' => 'datetime:d-m-Y H:i',
        'process_type' => PaymentProcessTypeEnum::class,
        'payment_type' => PaymentTypeEnum::class,
        'status' => PaymentStatusEnum::class
    ];

    
    protected $appends = ['receipt'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'account_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('payment_receipts')
            ->singleFile()
            ->useDisk('payment_receipts');
    }

    public function getReceiptAttribute()
    {
        $file = $this->getMedia('payment_receipts')->last();
        if ($file) {
            $file->url = $file->getUrl();
        }

        return $file;
    }
}
