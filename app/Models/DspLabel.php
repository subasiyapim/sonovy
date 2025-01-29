<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(array $attributes)
 * @method static where(string $column, mixed $operator = null, mixed $value = null)
 */
class DspLabel extends Model
{
    use HasFactory;

    protected $table = 'dsp_label';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label_id',
        'platform_id',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string', // Keeping status as a string for enum-like usage
    ];

    /**
     * Relationships
     */

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class, 'label_id');
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }

    /**
     * Get a human-readable status.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            '1' => 'Onay Bekliyor',
            '2' => 'Onaylandı',
            '3' => 'Reddedildi',
            default => 'Bilinmiyor',
        };
    }
}
