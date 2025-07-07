<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campus extends Model
{
    protected $fillable = [
        'name',
        'address'
    ];

    public function colleges(): HasMany
    {
        return $this->hasMany(College::class);
    }
}
