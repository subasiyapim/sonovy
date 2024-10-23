<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static find(mixed $id)
 */
class UserCode extends Model
{
    use HasFactory;

    protected $table = 'user_codes';

    protected $fillable = [
        'code',
        'user_id',
        'type'
    ];

    public $timestamps = false;

}
