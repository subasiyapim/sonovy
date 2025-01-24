<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SongComposer extends Model
{
    use HasFactory;

    protected $table = 'song_composers';
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
