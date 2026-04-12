<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseParticipant extends Model
{
    protected $fillable = ['case_id', 'user_id', 'role', 'notes'];

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}