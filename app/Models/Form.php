<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = [
        'form_number',
        'title',
        'purpose',
        'link',
        'revision'
    ];
}
