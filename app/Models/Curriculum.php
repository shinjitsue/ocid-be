<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Curriculum extends Model
{
    protected $table = 'curriculum';

    protected $fillable = [
        'program_id',
        'program_type',
        'file_path',
        'file_url',
        'file_name',
        'file_type',
        'file_size'
    ];

    public function graduateProgram(): BelongsTo
    {
        return $this->belongsTo(Graduate::class, 'program_id')
            ->where('curriculum.program_type', 'graduate');
    }

    public function undergradProgram(): BelongsTo
    {
        return $this->belongsTo(Undergrad::class, 'program_id')
            ->where('curriculum.program_type', 'undergrad');
    }

    // Add a polymorphic-like accessor
    public function getProgram()
    {
        if ($this->program_type === 'graduate') {
            return $this->graduateProgram;
        } else {
            return $this->undergradProgram;
        }
    }
}