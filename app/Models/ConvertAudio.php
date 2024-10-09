<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConvertAudio extends Model
{
    use HasFactory;

    protected $table = 'convert_audio';

    protected $fillable = [
        'product_id',
        'song_id',
        'release_date',
        'timezone_id',
        'release_time',
        'channel_theme_id',
        'output_file',
        'status',
        'error'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}
