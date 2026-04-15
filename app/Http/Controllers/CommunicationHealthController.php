<?php

namespace App\Http\Controllers;

use App\Models\InteractionLog;
use Illuminate\Http\Request;

class CommunicationHealthController extends Controller
{
    public function health(Request $request)
    {
        $hours = $request->get('hours', 24);

        $stats = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'period_hours' => $hours,
            'metrics' => $this->getHealthMetrics($hours),
        ];

        // Determine overall health status
        $failureRate = $stats['metrics']['failure_rate'];
        if ($failureRate > 0.2) { // More than 20% failure rate
            $stats['status'] = 'critical';
        } elseif ($failureRate > 0.1) { // More than 10% failure rate
            $stats['status'] = 'warning';
        }

        return response()->json($stats);
    }

    private function getHealthMetrics(int $hours): array
    {
        $startTime = now()->subHours($hours);

        $total = InteractionLog::where('created_at', '>=', $startTime)->count();
        $failed = InteractionLog::where('status', 'failed')
            ->where('created_at', '>=', $startTime)
            ->count();

        return [
            'total_messages' => $total,
            'failed_messages' => $failed,
            'failure_rate' => $total > 0 ? round($failed / $total, 4) : 0,
            'channels' => [
                'email' => InteractionLog::where('type', 'email')->where('created_at', '>=', $startTime)->count(),
                'sms' => InteractionLog::where('type', 'sms')->where('created_at', '>=', $startTime)->count(),
                'whatsapp' => InteractionLog::where('type', 'whatsapp')->where('created_at', '>=', $startTime)->count(),
            ],
            'services' => [
                'sms_enabled' => config('services.sms.api_key') && config('services.twilio.account_sid'),
                'email_enabled' => config('mail.mailers.smtp.transport'),
                'whatsapp_enabled' => config('services.twilio.whatsapp_number'),
            ],
        ];
    }
}
