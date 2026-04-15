<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Onboarding extends Model
{
    protected $fillable = ['client_id', 'status', 'completed_steps', 'completed_at'];

    protected function casts(): array
    {
        return [
            'completed_steps' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function getStepsAttribute()
    {
        return [
            'welcome_email' => 'Welcome email sent',
            'document_collection' => 'Client documents collected',
            'kyc_verification' => 'KYC verification completed',
            'engagement_letter' => 'Engagement letter signed',
            'initial_consultation' => 'Initial consultation scheduled',
        ];
    }

    public function isStepCompleted($step)
    {
        return in_array($step, $this->completed_steps ?? []);
    }

    public function completeStep($step)
    {
        $steps = $this->completed_steps ?? [];
        if (! in_array($step, $steps)) {
            $steps[] = $step;
            $this->update(['completed_steps' => $steps]);

            if (count($steps) === count($this->steps)) {
                $this->update(['status' => 'completed', 'completed_at' => now()]);
            }
        }
    }
}
