<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Hashtag extends Model
{
    use HasFactory;

    protected $table = 'hashtags';
    protected $fillable = ['model_id', 'model_type', 'name', 'code'];

    public function model()
    {
        return $this->morphTo();
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
