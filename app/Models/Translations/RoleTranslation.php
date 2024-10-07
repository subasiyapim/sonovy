<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleTranslation extends Model
{
    use HasFactory;

    protected $table = 'role_translations';

    protected $fillable = [
        'name'
    ];

    public $timestamps = false;
}
