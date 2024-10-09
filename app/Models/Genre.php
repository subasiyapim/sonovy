<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static upsert(mixed[] $toArray, string[] $array, string[] $array1)
 * @method static inRandomOrder()
 */
class Genre extends Model
{
    use HasFactory;

    protected $table = 'genres';

    protected $fillable = [
        'name',
        'code',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_genre', 'genre_id', 'product_id');
    }

}
