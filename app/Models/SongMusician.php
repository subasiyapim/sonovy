<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SongMusician extends Model
{
    use HasFactory;

    protected $table = 'song_musicians';
    protected $fillable = [
        'song_id',
        'name',
        'email',
        'phone',
        'address',
        'instrument',
    ];
    public $timestamps = false;

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}
