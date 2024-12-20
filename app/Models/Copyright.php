<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Copyright extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $fillable = [
        'product_type',
        'platform',
        'type',
        'product_id',
    ];

    protected $orderable = [
        'id',
        'product_type',
        'platform',
        'type',
        'product_id',
    ];

    protected $filterable = [
        'id',
        'product_type',
        'platform',
        'type',
        'product_id',
    ];

    public static array $COPYRIGHT_TYPES = [
        ["value" => 1, "label" => "UCG hak talebi: Gelirlendirme",],
        ["value" => 2, "label" => "UCG hak talebi: Engelleme",],
        ["value" => 3, "label" => "UCG hak talebi: İhtar",],
        ["value" => 4, "label" => "UCG hak talebi: İptal",],
        ["value" => 5, "label" => "Diğer itirazlar",],
    ];

    public static array $PLATFORM_TO_REJECT = [
        ["value" => 1, "label" => "Youtube",],
        ["value" => 2, "label" => "Tiktok",],
        ["value" => 3, "label" => "Meta",],
    ];

    public function copyrightSongs(): HasMany
    {
        return $this->hasMany(CopyrightSong::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

}
