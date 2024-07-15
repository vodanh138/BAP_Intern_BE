<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Show extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
    ];
    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
