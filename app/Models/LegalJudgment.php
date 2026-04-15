<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalJudgment extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_number',
        'case_title',
        'court',
        'state',
        'bench',
        'judges',
        'judgment_date',
        'facts',
        'issues',
        'arguments_petitioner',
        'arguments_respondent',
        'judgment_summary',
        'full_judgment',
        'citation',
        'outcome',
        'legal_principles',
        'statutes_referred',
        'precedent_cases',
        'dissenting_opinion',
        'reporter',
        'reporter_volume',
        'reporter_page',
        'is_landmark',
        'is_active',
    ];

    protected $casts = [
        'judgment_date' => 'date',
        'judges' => 'array',
        'legal_principles' => 'array',
        'statutes_referred' => 'array',
        'precedent_cases' => 'array',
        'is_landmark' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function references(): HasMany
    {
        return $this->hasMany(LegalReference::class, 'source_id')->where('source_type', 'judgment');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLandmark($query)
    {
        return $query->where('is_landmark', true);
    }

    public function scopeByCourt($query, $court)
    {
        return $query->where('court', $court);
    }

    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    public function scopeByOutcome($query, $outcome)
    {
        return $query->where('outcome', $outcome);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('judgment_date', $year);
    }
}
