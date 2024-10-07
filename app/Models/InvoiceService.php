<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InvoiceService extends Model
{
    use HasFactory;

    protected $table = 'invoice_services';

    protected $fillable = [
        'invoice_id',
        'service_id',
        'quantity',
        'price',
        'total',
        'tax_rate',
        'tax_withholding_rate',
        'tax_withholding_reason',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
