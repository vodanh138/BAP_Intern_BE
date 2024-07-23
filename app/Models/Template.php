<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'headerType',
        'footerType',
        'title',
        'headerBgColor',
        'headerTextColor',
        'footer',
        'footerBgColor',
        'footerTextColor',
        'avaPath',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function section()
    {
        return $this->hasMany(Section::class);
    }
}
