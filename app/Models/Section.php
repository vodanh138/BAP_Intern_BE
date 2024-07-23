<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'content1',
        'content2',
        'bgColor',
        'textColor',
        'template_id',
    ];
    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
