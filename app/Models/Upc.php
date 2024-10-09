<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Upc extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $table = 'upcs';

    protected $fillable = ['upc', 'product_id', 'use_by_date'];

    protected array $filterable = ['upc', 'use_by_date', 'broadcast.name'];

    protected array $orderable = ['upc', 'use_by_date', 'broadcast.name'];

    protected $casts = [
        'use_by_date' => 'date',
    ];

    public function scopeUsedUpcs($query)
    {
        return $query->whereNotNull('use_by_date')->whereHas('broadcast');
    }

    public function scopeNotUsedUpcs($query)
    {
        return $query->whereNull('use_by_date');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d-m-Y');
    }

}
