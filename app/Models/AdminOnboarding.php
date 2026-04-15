<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminOnboarding extends Model
{
    protected $fillable = [
        'admin_id', 'is_completed', 'completed_steps', 'current_step', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_steps' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getSteps(): array
    {
        return [
            1 => [
                'title' => 'Welcome & System Overview',
                'description' => 'Get familiar with your legal practice management system',
                'action' => 'Continue',
            ],
            2 => [
                'title' => 'Create Your First Attorney',
                'description' => 'Add attorneys to your legal team',
                'action' => 'Add Attorney',
            ],
            3 => [
                'title' => 'Set Up Case Types',
                'description' => 'Configure case categories for your practice',
                'action' => 'Configure Cases',
            ],
            4 => [
                'title' => 'Configure Billing',
                'description' => 'Set up billing rates and GST compliance',
                'action' => 'Setup Billing',
            ],
            5 => [
                'title' => 'Explore Features',
                'description' => 'Take a tour of all available features',
                'action' => 'Start Tour',
            ],
        ];
    }

    public function getCurrentStepData(): array
    {
        $steps = $this->getSteps();

        return $steps[$this->current_step] ?? $steps[1];
    }

    public function completeStep(int $step): bool
    {
        if ($step === $this->current_step) {
            $completedSteps = $this->completed_steps ?? [];
            $completedSteps[] = $step;

            $this->update([
                'completed_steps' => $completedSteps,
                'current_step' => min($step + 1, count($this->getSteps())),
            ]);

            // Check if all steps are completed
            if (count($completedSteps) === count($this->getSteps())) {
                $this->update([
                    'is_completed' => true,
                    'completed_at' => now(),
                ]);
            }

            return true;
        }

        return false;
    }

    public function isStepCompleted(int $step): bool
    {
        return in_array($step, $this->completed_steps ?? []);
    }

    public static function startOnboarding(User $admin): self
    {
        return self::create([
            'admin_id' => $admin->id,
            'current_step' => 1,
            'completed_steps' => [],
        ]);
    }
}
