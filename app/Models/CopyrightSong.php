<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CopyrightSong extends Model
{
    use HasFactory;

    protected $fillable = [
        'copyright_id',
        'song_id',
        'url',
    ];

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function copyright(): BelongsTo
    {
        return $this->belongsTo(Copyright::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
