<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $table = 'invoices';

    protected $fillable = [
        'invoice_type',
        'invoice_number',
        'name',
        'invoice_date',
        'invoice_time',
        'tax_office',
        'tax_number',
        'commercial_register_number',
        'country_id',
        'state_id',
        'city_id',
        'zip_code',
        'address',
        'note',
        'phone',
    ];

    protected array $filterable = [
        'invoice_type',
        'invoice_number',
        'name',
        'invoice_date',
        'invoice_time',
        'tax_office',
        'tax_number',
        'commercial_register_number',
        'country_id',
        'state_id',
        'city_id',
        'zip_code',
        'address',
        'note',
        'phone',
    ];
    protected array $orderable = [
        'invoice_type',
        'invoice_number',
        'name',
        'invoice_date',
        'invoice_time',
        'tax_office',
        'tax_number',
        'commercial_register_number',
        'country_id',
        'state_id',
        'city_id',
        'zip_code',
        'address',
        'note',
        'phone',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(InvoiceService::class);
    }


}
