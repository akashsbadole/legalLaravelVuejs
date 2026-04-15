<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StateLaw extends Model
{
    use HasFactory;

    protected $fillable = [
        'state',
        'law_name',
        'law_type',
        'section_number',
        'subsection',
        'content',
        'description',
        'enactment_date',
        'amendments',
        'is_active',
    ];

    protected $casts = [
        'enactment_date' => 'date',
        'amendments' => 'array',
        'is_active' => 'boolean',
    ];

    public function references(): HasMany
    {
        return $this->hasMany(LegalReference::class, 'source_id')->where('source_type', 'state_law');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    public function scopeByLawType($query, $lawType)
    {
        return $query->where('law_type', $lawType);
    }
}
