<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Title extends Model implements TranslatableContract
{
    use HasFactory;
    use HasAdvancedFilter;
    use Translatable;

    protected $table = 'titles';
    public array $translatedAttributes = ['title'];

    protected array $filterable = ['id', 'translations.title'];
    protected array $orderable = ['id', 'translations.title'];
}
