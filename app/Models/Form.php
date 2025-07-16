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
        'revision',
        'file_path',
        'file_url',
        'file_name',
        'file_type',
        'file_size'
    ];
}
