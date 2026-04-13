<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:send-pending-follow-ups')]
#[Description('Command description')]
class SendPendingFollowUps extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingFollowUps = \App\Models\FollowUp::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($pendingFollowUps as $followUp) {
            \App\Jobs\SendFollowUp::dispatch($followUp);
        }

        $this->info("Dispatched {$pendingFollowUps->count()} follow-ups.");
    }
}
