<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SongWriter extends Model
{
    use HasFactory;

    protected $table = 'song_writers';
    public $timestamps = false;
    protected $fillable = [
        'song_id',
        'name',
        'email',
        'phone',
        'address',
    ];
    
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}
