<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogNumber extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $fillable = [
        'catalog_number',
        'is_used',
        'product_id',
    ];

    protected $casts = [
        'is_used' => 'boolean',
    ];

    protected $filterable = [
        'catalog_number',
        'is_used',
        'product.name'
    ];

    protected $orderable = [
        'catalog_number',
        'is_used',
        'product.name'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
