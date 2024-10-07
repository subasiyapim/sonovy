<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusixMatach extends Model
{
    use HasFactory;

    protected $table = 'musix_mataches';

    protected $guarded = [];

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }


}
