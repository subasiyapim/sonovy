<?php

namespace App\Models;

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
        'earning_report_file_id',
        'user_id',
        'name',
        'status',
        'report_date',
        'sales_date',
        'platform',
        'platform_id',
        'country',
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
        'unit_price',
        'net_revenue',
    ];

    protected array $filterable = [
        'status',
        'user_id',
        'name',
        'report_date',
        'sales_date',
        'platform',
        'platform.name',
        'country',
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
        'release_type',
        'sales_type',
        'quantity',
        'currency',
        'unit_price',
        'net_revenue',
    ];

    protected array $orderable = [
        'status',
        'user_id',
        'name',
        'report_date',
        'sales_date',
        'platform',
        'platform.name',
        'country',
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
        'release_type',
        'sales_type',
        'quantity',
        'currency',
        'unit_price',
        'net_revenue',
    ];

    protected $casts = [
        'report_date' => 'date',
        'sales_date' => 'date',
        'quantity' => 'integer',
        'unit_price' => 'decimal:15',
        'net_revenue' => 'decimal:15',
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
