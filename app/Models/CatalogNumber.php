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
        'broadcast_id',
    ];

    protected $casts = [
        'is_used' => 'boolean',
    ];

    protected $filterable = [
        'catalog_number',
        'is_used',
        'broadcast.name'
    ];

    protected $orderable = [
        'catalog_number',
        'is_used',
        'broadcast.name'
    ];

    public function broadcast(): BelongsTo
    {
        return $this->belongsTo(Broadcast::class);
    }
}
