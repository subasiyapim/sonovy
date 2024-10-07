<?php

namespace App\Models\Translations;

use App\Models\Title;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistBranchTranslation extends Model
{
    use HasFactory;

    protected $table = 'artist_branch_translations';
    public $timestamps = false;

    protected $fillable = ['name'];

}
