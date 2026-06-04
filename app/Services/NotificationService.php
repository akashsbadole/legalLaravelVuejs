<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use App\Mail\TaskAssignedMail;
use App\Mail\CaseAssignedMail;
use App\Mail\InvoiceGeneratedMail;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Pusher\Pusher;

class NotificationService
{
    private ?Pusher $pusher;

    private string $appKey;

    private string $appCluster;

    public function __construct()
    {
        $this->appKey = config('broadcasting.connections.pusher.key');
        $this->appCluster = config('broadcasting.connections.pusher.cluster');

        if ($this->appKey) {
            $this->pusher = new Pusher(
                $this->appKey,
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                [
                    'cluster' => $this->appCluster,
                    'useTLS' => true,
                ]
            );
        }
    }

    public function isEnabled(): bool
    {
        return ! empty($this->pusher);
    }

    public function send(int $userId, string $title, string $message, string $type = 'info', array $data = []): ?AppNotification
    {
        $notification = AppNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
        ]);

        if ($this->isEnabled()) {
            $this->broadcast($userId, [
                'id' => $notification->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => $data,
                'created_at' => $notification->created_at->toIso8601String(),
            ]);
        }

        return $notification;
    }

    public function sendToMany(array $userIds, string $title, string $message, string $type = 'info'): int
    {
        $count = 0;
        foreach ($userIds as $userId) {
            if ($this->send($userId, $title, $message, $type)) {
                $count++;
            }
        }

        return $count;
    }

    public function broadcast(int $userId, array $data): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        try {
            $this->pusher->trigger(
                'user-'.$userId,
                'notification',
                $data
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Pusher broadcast error: '.$e->getMessage());

            return false;
        }
    }

    public function broadcastToChannel(string $channel, string $event, array $data): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        try {
            $this->pusher->trigger($channel, $event, $data);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function notifyNewCase(int $userId, array $caseData): ?AppNotification
    {
        $notification = $this->send(
            $userId,
            'New Case Assigned',
            "You have been assigned to case: {$caseData['case_number']}",
            'case',
            $caseData
        );

        $user = User::find($userId);
        if ($user && $user->email) {
            try {
                Mail::to($user->email)->send(new CaseAssignedMail($user, $caseData));
            } catch (\Exception $e) {
                Log::error('Case assignment email failed: ' . $e->getMessage());
            }
        }

        return $notification;
    }

    public function notifyTaskAssigned(int $userId, array $taskData): ?AppNotification
    {
        $notification = $this->send(
            $userId,
            'New Task Assigned',
            "You have been assigned a new task: {$taskData['title']}",
            'task',
            $taskData
        );

        $user = User::find($userId);
        if ($user && $user->email) {
            try {
                Mail::to($user->email)->send(new TaskAssignedMail($user, $taskData));
            } catch (\Exception $e) {
                Log::error('Task assignment email failed: ' . $e->getMessage());
            }
        }

        return $notification;
    }

    public function notifyInvoiceReceived(int $userId, array $invoiceData): ?AppNotification
    {
        $notification = $this->send(
            $userId,
            'New Invoice',
            "Invoice #{$invoiceData['invoice_number']} has been generated",
            'invoice',
            $invoiceData
        );

        $user = User::find($userId);
        if ($user && $user->email) {
            try {
                Mail::to($user->email)->send(new InvoiceGeneratedMail($user, $invoiceData));
            } catch (\Exception $e) {
                Log::error('Invoice email failed: ' . $e->getMessage());
            }
        }

        return $notification;
    }

    public function notifyDocumentShared(int $userId, array $documentData): ?AppNotification
    {
        return $this->send(
            $userId,
            'Document Shared',
            "A document has been shared with you: {$documentData['title']}",
            'document',
            $documentData
        );
    }

    public function notifyDeadline(int $userId, array $deadlineData): ?AppNotification
    {
        return $this->send(
            $userId,
            'Deadline Reminder',
            "{$deadlineData['title']} is due on {$deadlineData['due_date']}",
            'deadline',
            $deadlineData
        );
    }

    public static function channels(int $userId): array
    {
        return [
            new PrivateChannel('user.'.$userId),
        ];
    }
}
