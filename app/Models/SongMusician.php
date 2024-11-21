<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongMusician extends Model
{
    protected $table = 'song_musicians';
    protected $fillable = [
        'song_id',
        'name',
        'role_id'
    ];
    public $timestamps = false;

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'song_id');
    }
}
