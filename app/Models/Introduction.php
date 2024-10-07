<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Introduction extends Model
{
    use HasFactory;

    protected $fillable = ['broadcast_id', 'language_id', 'p_line', 'description'];

    public function broadcast(): BelongsTo
    {
        return $this->belongsTo(Broadcast::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

}
