<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class College extends Model
{
    protected $fillable = [
        'name',
        'acronym',
        'campus_id',
        'logo_path',
        'logo_url',
        'logo_name',
        'logo_type',
        'logo_size'
    ];

    protected $appends = ['logo'];

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

    /**
     * Get the logo URL or return null if no logo uploaded
     */
    public function getLogoAttribute(): ?string
    {
        return $this->logo_url;
    }

    /**
     * Check if college has a custom uploaded logo
     */
    public function hasCustomLogo(): bool
    {
        return !empty($this->logo_url);
    }

    /**
     * Get logo or default placeholder
     */
    public function getLogoOrDefault(): string
    {
        return $this->logo_url ?? '/images/default-college-logo.png';
    }
}
