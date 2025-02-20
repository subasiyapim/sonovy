<?php

namespace App\Models;

use App\Models\System\Country;
use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rules\Enum;

class EarningReport extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $table = 'earning_reports';

    protected $fillable = [
        'user_id',
        'name',
        'period',
        'report_type',
        'file_size',
        'status',
        'processed_at',
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
        'streaming_type',
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
        'net_revenue',
    ];

    protected array $filterable = [
        'status',
        'user_id',
        'name',
        'report_date',
        'reporting_month',
        'sales_date',
        'sales_month',
        'platform',
        'platform.name',
        'country',
        'region',
        'country.name',
        'label_name',
        'label.name',
        'artist_name',
        'artist.name',
        'release_name',
        'song_name',
        'song.name',
        'upc_code',
        'isrc_code',
        'catalog_number',
        'streaming_type',
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
        'net_revenue',
    ];

    protected array $orderable = [
        'status',
        'user_id',
        'name',
        'report_date',
        'reporting_month',
        'sales_date',
        'sales_month',
        'platform',
        'platform.name',
        'country',
        'region',
        'country.name',
        'label_name',
        'label.name',
        'artist_name',
        'artist.name',
        'release_name',
        'song_name',
        'song.name',
        'upc_code',
        'isrc_code',
        'catalog_number',
        'streaming_type',
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
        'net_revenue',
    ];

    protected $casts = [
        'report_date' => 'date',
        'sales_date' => 'date',
        'quantity' => 'integer',
        'unit_price' => 'decimal:12',
        'mechanical_fee' => 'decimal:12',
        'gross_revenue' => 'decimal:12',
        'client_share_rate' => 'decimal:2',
        'net_revenue' => 'decimal:12',
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(EarningReportFile::class, 'earning_report_file_id');
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


    public function earnings(): HasMany
    {
        return $this->hasMany(Earning::class, 'earning_report_id');
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d-m-Y H:i');
    }

}
