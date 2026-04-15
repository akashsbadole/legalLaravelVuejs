<?php

namespace App\Console\Commands;

use App\Models\InteractionLog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('communication:monitor {--hours=24 : Hours to look back for undelivered messages} {--alert : Send alerts for failed messages}')]
#[Description('Monitor communication delivery status and alert on failures')]
class MonitorCommunicationStatus extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $alert = $this->option('alert');

        $this->info("Monitoring communication status for the last {$hours} hours...");

        // Get statistics
        $stats = $this->getCommunicationStats($hours);

        $this->displayStats($stats);

        // Check for issues
        $issues = $this->identifyIssues($stats);

        if (! empty($issues)) {
            $this->warn('Issues detected:');
            foreach ($issues as $issue) {
                $this->line("  - {$issue}");
            }

            if ($alert) {
                $this->sendAlerts($issues);
            }
        } else {
            $this->info('All communication channels appear healthy.');
        }

        return 0;
    }

    private function getCommunicationStats(int $hours): array
    {
        $startTime = now()->subHours($hours);

        return [
            'total_sent' => InteractionLog::where('created_at', '>=', $startTime)->count(),
            'by_channel' => [
                'email' => InteractionLog::where('type', 'email')
                    ->where('created_at', '>=', $startTime)
                    ->count(),
                'sms' => InteractionLog::where('type', 'sms')
                    ->where('created_at', '>=', $startTime)
                    ->count(),
                'whatsapp' => InteractionLog::where('type', 'whatsapp')
                    ->where('created_at', '>=', $startTime)
                    ->count(),
            ],
            'by_status' => [
                'sent' => InteractionLog::where('status', 'sent')
                    ->where('created_at', '>=', $startTime)
                    ->count(),
                'delivered' => InteractionLog::where('status', 'delivered')
                    ->where('created_at', '>=', $startTime)
                    ->count(),
                'failed' => InteractionLog::where('status', 'failed')
                    ->where('created_at', '>=', $startTime)
                    ->count(),
            ],
            'recent_failures' => InteractionLog::where('status', 'failed')
                ->where('created_at', '>=', $startTime)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),
        ];
    }

    private function displayStats(array $stats): void
    {
        $this->info('Communication Statistics:');
        $this->line("Total messages sent: {$stats['total_sent']}");

        $this->info('By Channel:');
        foreach ($stats['by_channel'] as $channel => $count) {
            $this->line("  {$channel}: {$count}");
        }

        $this->info('By Status:');
        foreach ($stats['by_status'] as $status => $count) {
            $this->line("  {$status}: {$count}");
        }
    }

    private function identifyIssues(array $stats): array
    {
        $issues = [];

        $total = $stats['total_sent'];
        $failed = $stats['by_status']['failed'];

        if ($total > 0 && ($failed / $total) > 0.1) { // More than 10% failure rate
            $issues[] = "High failure rate: {$failed} of {$total} messages failed (".round(($failed / $total) * 100, 1).'%)';
        }

        if ($stats['by_channel']['whatsapp'] > 0 && $stats['by_channel']['sms'] == 0) {
            $issues[] = 'SMS fallback not being used despite WhatsApp messages being sent';
        }

        if (! empty($stats['recent_failures'])) {
            $issues[] = count($stats['recent_failures']).' recent message failures detected';
        }

        return $issues;
    }

    private function sendAlerts(array $issues): void
    {
        $message = "Communication System Alert:\n".implode("\n", $issues);

        // Log the alert
        Log::warning('Communication system alert', ['issues' => $issues]);

        // In a real application, you might send notifications via email, Slack, etc.
        // For now, just output to console
        $this->error('ALERT: '.$message);
    }
}
