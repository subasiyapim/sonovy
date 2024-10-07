<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class MailTemplate extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;
    use HasAdvancedFilter;

    protected $table = 'mail_templates';
    protected $fillable = ['code', 'name'];
    public array $translatedAttributes = ['subject', 'body'];

    protected $filterable = ['id', 'subject', 'body', 'created_at', 'updated_at'];
    protected $orderable = ['id', 'subject', 'body', 'created_at', 'updated_at'];

    public function render(array $params = [])
    {
        $template = $this->body;

        foreach ($params as $key => $value) {
            $template = str_replace('{{'.$key.'}}', $value, $template);
        }

        return $template;
    }

}
