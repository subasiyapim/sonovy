<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @method static advancedFilter()
 * @method static create(mixed $validated)
 * @method static inRandomOrder()
 * @method static where(string $string, string $string1, string $string2)
 */
class SiteFeature extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;
    use HasAdvancedFilter;

    protected $table = 'site_features';

    protected $fillable = [
        'icon',
        'title',
        'text'
    ];

    protected array $orderable = [
        'id',
        'title',
        'text'
    ];

    protected array $filterable = [
        'id',
        'title',
        'text'
    ];

    protected $casts = [
        'title' => 'array',
        'text' => 'array',
    ];

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
