<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

class PlanItem extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;
    use HasAdvancedFilter;

    protected $table = 'plan_items';
    public array $translatedAttributes = ['name'];

    protected $fillable = ['category', 'type', 'is_deletable', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected array $filterable = [
        'id',
        'category',
        'type',
        'translations.name',

    ];

    protected array $orderable = [
        'id',
        'category',
        'type',
        'name',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'item_plan')
            ->withPivot('value', 'note')
            ->where('plan_items.is_active', 1);
    }


}
