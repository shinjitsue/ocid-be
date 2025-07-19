<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Undergrad extends Model
{
    protected $fillable = [
        'program_name',
        'acronym',
        'college_id'
    ];

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    public function curriculum(): HasOne
    {
        return $this->hasOne(Curriculum::class, 'program_id')
            ->where('program_type', 'undergrad');
    }

    public function syllabus(): HasOne
    {
        return $this->hasOne(Syllabus::class, 'program_id')
            ->where('program_type', 'undergrad');
    }
}
