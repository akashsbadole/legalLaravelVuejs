<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrpcSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_number',
        'subsection',
        'title',
        'description',
        'procedure',
        'court_level',
        'timeline_requirements',
        'jurisdiction_details',
        'related_ipc_sections',
        'is_active',
    ];

    protected $casts = [
        'related_ipc_sections' => 'array',
        'is_active' => 'boolean',
    ];

    public function references(): HasMany
    {
        return $this->hasMany(LegalReference::class, 'source_id')->where('source_type', 'crpc');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCourtLevel($query, $courtLevel)
    {
        return $query->where('court_level', $courtLevel);
    }
}
