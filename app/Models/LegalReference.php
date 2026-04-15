<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_type',
        'source_id',
        'reference_type',
        'reference_citation',
        'reference_context',
        'url',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function source(): BelongsTo
    {
        return match ($this->source_type) {
            'ipc' => $this->belongsTo(IpcSection::class, 'source_id'),
            'crpc' => $this->belongsTo(CrpcSection::class, 'source_id'),
            'cpc' => $this->belongsTo(CpcSection::class, 'source_id'),
            'constitution' => $this->belongsTo(ConstitutionArticle::class, 'source_id'),
            'state_law' => $this->belongsTo(StateLaw::class, 'source_id'),
            'judgment' => $this->belongsTo(LegalJudgment::class, 'source_id'),
            default => null
        };
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeBySourceType($query, $sourceType)
    {
        return $query->where('source_type', $sourceType);
    }

    public function scopeByReferenceType($query, $referenceType)
    {
        return $query->where('reference_type', $referenceType);
    }
}
