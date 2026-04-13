<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    protected $fillable = ['title', 'description', 'questions', 'is_active'];

    protected function casts(): array
    {
        return [
            'questions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }
}
