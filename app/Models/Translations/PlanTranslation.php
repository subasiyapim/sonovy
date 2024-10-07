<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'plan_translations';

    protected $fillable = [
        'name',
        'description'
    ];
}
