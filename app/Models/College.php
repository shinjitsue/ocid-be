<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class College extends Model
{
    protected $fillable = [
        'name',
        'campus_id'
    ];

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function undergrads(): HasMany
    {
        return $this->hasMany(Undergrad::class);
    }

    public function graduates(): HasMany
    {
        return $this->hasMany(Graduate::class);
    }
}
