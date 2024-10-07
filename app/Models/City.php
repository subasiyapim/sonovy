<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static firstOrCreate(array $array, array $array1)
 */
class City extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'country_id',
        'name',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }


}
