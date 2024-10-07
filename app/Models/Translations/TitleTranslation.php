<?php

namespace App\Models\Translations;

use App\Models\Title;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitleTranslation extends Model
{
    use HasFactory;

    protected $table = 'title_translations';
    public $timestamps = false;

    protected $fillable = ['title'];

}
