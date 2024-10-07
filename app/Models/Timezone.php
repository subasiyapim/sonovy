<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static get()
 */
class Timezone extends Model
{
    use HasFactory;

    protected $table = 'timezones';
    protected $fillable = ['zone', 'gmt', 'name'];

    public $timestamps = false;
}
