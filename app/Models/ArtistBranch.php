<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

/**
 * @method static inRandomOrder()
 * @method static get(string[] $array)
 * @method static updateOrCreate(array $array, array $array1)
 */
class ArtistBranch extends Model implements TranslatableContract
{
    use HasFactory;
    use HasAdvancedFilter;
    use Translatable;

    protected $table = 'artist_branches';
    public array $translatedAttributes = ['name'];

    protected array $filterable = ['id', 'translations.name'];
    protected array $orderable = ['id', 'translations.name'];
}
