<?php

namespace App\Models;

use App\Enums\FeaturePeriodEnum;
use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Feature extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
        'is_deletable' => 'boolean',
        'period' => FeaturePeriodEnum::class
    ];

    protected $appends = [
        'period_title'
    ];

    protected $fillable = [
        'plan_item_id',
        'period',
        'limit',
        'title',
        'description',
        'type',
        'is_active',
        'is_deletable',
        'amount',
    ];

    protected array $orderable = [
        'id',
        'title',
        'type',
        'is_active',
    ];

    protected array $filterable = [
        'id',
        'title',
        'description',
        'type',
        'is_active',
        'is_deletable',
    ];

    //Active Scope
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(PlanItem::class, 'plan_item_id');
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'model_type');
    }

    protected function periodTitle(): Attribute
    {
        return Attribute::make(
            get: fn() => FeaturePeriodEnum::getTitles()[$this->period->value]
        );
    }
}
