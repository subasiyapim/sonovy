<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongWriter extends Model
{
    protected $table = 'song_writers';

    protected $fillable = [
        'song_id',
        'name',
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'song_id');
    }
}
