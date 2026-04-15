<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CpcSection extends Model
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
        'monetary_jurisdiction',
        'appeal_provisions',
        'related_sections',
        'is_active',
    ];

    protected $casts = [
        'monetary_jurisdiction' => 'decimal:2',
        'related_sections' => 'array',
        'is_active' => 'boolean',
    ];

    public function references(): HasMany
    {
        return $this->hasMany(LegalReference::class, 'source_id')->where('source_type', 'cpc');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCourtLevel($query, $courtLevel)
    {
        return $query->where('court_level', $courtLevel);
    }

    public function scopeByMonetaryJurisdiction($query, $minAmount, $maxAmount = null)
    {
        $query->where('monetary_jurisdiction', '>=', $minAmount);
        if ($maxAmount) {
            $query->where('monetary_jurisdiction', '<=', $maxAmount);
        }

        return $query;
    }
}
