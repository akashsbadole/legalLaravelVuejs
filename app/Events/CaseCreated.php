<?php

namespace App\Events;

use App\Models\LegalCase;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CaseCreated
{
    use Dispatchable, SerializesModels;

    public LegalCase $case;

    /**
     * Create a new event instance.
     */
    public function __construct(LegalCase $case)
    {
        $this->case = $case;
    }
}