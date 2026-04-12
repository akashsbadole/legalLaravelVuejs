<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseStatus extends Model
{
    protected $fillable = ['name', 'description', 'color'];

    public function cases(): HasMany
    {
        return $this->hasMany(LegalCase::class, 'status_id');
    }
}