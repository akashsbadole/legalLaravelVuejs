<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IpcSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_number',
        'subsection',
        'title',
        'description',
        'penalty',
        'classification',
        'cognizable',
        'punishment_details',
        'related_sections',
        'is_active',
    ];

    protected $casts = [
        'related_sections' => 'array',
        'is_active' => 'boolean',
    ];

    public function references(): HasMany
    {
        return $this->hasMany(LegalReference::class, 'source_id')->where('source_type', 'ipc');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCognizable($query)
    {
        return $query->where('cognizable', 'cognizable');
    }

    public function scopeBailable($query)
    {
        return $query->where('classification', 'bailable');
    }
}
