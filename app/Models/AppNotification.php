<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = ['user_id', 'type', 'title', 'message', 'link', 'is_read'];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createForUser(int $userId, string $type, string $title, ?string $message = null, ?string $link = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    public static function notifyCaseUpdate(int $caseId, string $caseNumber, int $userId): void
    {
        self::createForUser(
            $userId,
            'case_update',
            "Case Updated: {$caseNumber}",
            "Case {$caseNumber} has been updated.",
            "/cases/{$caseId}"
        );
    }

    public static function notifyTaskAssigned(int $taskId, string $taskTitle, int $userId): void
    {
        self::createForUser(
            $userId,
            'task_assigned',
            "New Task Assigned: {$taskTitle}",
            'You have been assigned a new task.',
            "/tasks/{$taskId}"
        );
    }

    public static function notifyInvoiceReceived(int $invoiceId, string $invoiceNumber, int $userId): void
    {
        self::createForUser(
            $userId,
            'invoice_received',
            "New Invoice: {$invoiceNumber}",
            'You have received a new invoice.',
            "/portal/invoices/{$invoiceId}"
        );
    }

    public static function notifyNewMessage(int $caseId, int $userId): void
    {
        self::createForUser(
            $userId,
            'new_message',
            'New Message Received',
            'You have a new message in your case.',
            "/portal/cases/{$caseId}"
        );
    }

    public static function notifyNewDocument(int $userId, string $documentName, string $caseNumber): void
    {
        self::createForUser(
            $userId,
            'new_document',
            "New Document Available: {$documentName}",
            "A new document {$documentName} has been added to case {$caseNumber}.",
            '/portal/documents'
        );
    }
}
