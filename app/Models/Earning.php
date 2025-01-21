<?php

namespace App\Models;

use App\Models\System\Country;
use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Earning extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $fillable = [
        'earning_report_id',
        'user_id',
        'name',
        'status',
        'report_date',
        'reporting_month',
        'sales_date',
        'sales_month',
        'platform',
        'platform_id',
        'country',
        'region',
        'country_id',
        'label_name',
        'label_id',
        'artist_name',
        'artist_id',
        'release_name',
        'song_name',
        'song_id',
        'upc_code',
        'isrc_code',
        'catalog_number',
        'streaming_subscription_type',
        'release_type',
        'sales_type',
        'quantity',
        'currency',
        'client_payment_currency',
        'unit_price',
        'mechanical_fee',
        'gross_revenue',
        'client_share_rate',
        'earning',
    ];

    protected array $filterable = [
        'earning_report_id',
        'artist.name',
        'song.name',
        'report_date',
        'sales_date',
        'country',
        'platform',
        'user_id',
        'earning'
    ];

    protected array $orderable = [
        'earning_report_id',
        'artist.name',
        'song.name',
        'report_date',
        'sales_date',
        'country',
        'platform',
        'user_id',
        'earning'
    ];

    protected $casts = [
        'sales_date' => 'date',
    ];


    public function report(): BelongsTo
    {
        return $this->belongsTo(EarningReport::class, 'earning_report_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'upc_code', 'upc_code')
            ->whereNotNull('upc_code');
    }

    public function uploadedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_user_id');
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i');
    }

}
