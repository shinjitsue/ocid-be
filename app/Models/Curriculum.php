<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Curriculum extends Model
{
    protected $fillable = [
        'image_url',
        'program_id',
        'program_type'
    ];

    public function graduateProgram(): BelongsTo
    {
        return $this->belongsTo(Graduate::class, 'program_id')
            ->when($this->program_type === 'graduate');
    }

    public function undergradProgram(): BelongsTo
    {
        return $this->belongsTo(Undergrad::class, 'program_id')
            ->when($this->program_type === 'undergrad');
    }
}
