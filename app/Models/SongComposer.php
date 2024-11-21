<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongComposer extends Model
{
    protected $table = 'song_composers';

    protected $fillable = [
        'song_id',
        'name',
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'song_id');
    }

}
