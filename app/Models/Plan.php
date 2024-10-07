<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;

class Plan extends Model implements TranslatableContract
{
    use HasFactory;
    use HasAdvancedFilter;
    use Translatable;

    protected $fillable = [
        'monthly_price',
        'annual_price',
        'sort_order',
        'is_active',
        'preferred'
    ];

    public array $translatedAttributes = ['name', 'description'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected array $orderable = [
        'id',
        'monthly_price',
        'annual_price',
        'sort_order',
        'is_active',
        'preferred'
    ];

    protected array $filterable = [
        'id',
        'monthly_price',
        'annual_price',
        'sort_order',
        'is_active',
        'preferred'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(PlanItem::class, 'item_plan')
            ->withPivot('value', 'note')
            ->where('plan_items.is_active', 1);
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'model_type');
    }
}
