<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    private ?string $mailDriver;

    private ?string $fromAddress;

    private ?string $fromName;

    public function __construct()
    {
        $this->mailDriver = config('mail.mailers.smtp.transport');
        $this->fromAddress = config('mail.from.address');
        $this->fromName = config('mail.from.name');
    }

    public function isEnabled(): bool
    {
        return ! empty($this->fromAddress);
    }

    public function send(array $to, string $subject, string $body, array $attachments = []): bool
    {
        if (! $this->isEnabled()) {
            Log::warning('Email service not configured');

            return false;
        }

        try {
            Mail::html($body, function ($message) use ($to, $subject, $attachments) {
                $message->from($this->fromAddress, $this->fromName)
                    ->to($to['email'], $to['name'] ?? null)
                    ->subject($subject);

                foreach ($attachments as $attachment) {
                    if (isset($attachment['path'])) {
                        $message->attach($attachment['path'], [
                            'as' => $attachment['name'] ?? basename($attachment['path']),
                        ]);
                    }
                }
            });

            Log::info("Email sent to {$to['email']}: {$subject}");

            return true;
        } catch (\Exception $e) {
            Log::error('Email send failed: '.$e->getMessage());

            return false;
        }
    }

    public function sendTemplate(string $templateSlug, array $to, array $variables = []): bool
    {
        $template = EmailTemplate::where('slug', $templateSlug)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            Log::warning("Email template not found: {$templateSlug}");

            return false;
        }

        $rendered = $template->render($variables);

        return $this->send(
            $to,
            $rendered['subject'],
            $rendered['body'],
            $variables['attachments'] ?? []
        );
    }

    public function sendCaseAssigned(array $to, array $caseData): bool
    {
        return $this->sendTemplate('case_assigned', $to, [
            'case_number' => $caseData['case_number'],
            'case_title' => $caseData['title'],
            'attorney_name' => $caseData['attorney_name'],
            'client_name' => $caseData['client_name'],
        ]);
    }

    public function sendInvoice(array $to, array $invoiceData): bool
    {
        return $this->sendTemplate('invoice_generated', $to, [
            'invoice_number' => $invoiceData['invoice_number'],
            'amount' => $invoiceData['amount'],
            'due_date' => $invoiceData['due_date'],
            'client_name' => $invoiceData['client_name'],
        ]);
    }

    public function sendTaskAssigned(array $to, array $taskData): bool
    {
        return $this->sendTemplate('task_assigned', $to, [
            'task_title' => $taskData['title'],
            'due_date' => $taskData['due_date'],
            'case_number' => $taskData['case_number'],
            'assignee_name' => $taskData['assignee_name'],
        ]);
    }

    public function sendLeadAssigned(array $to, array $leadData): bool
    {
        return $this->sendTemplate('lead_assigned', $to, [
            'lead_name' => $leadData['name'],
            'lead_email' => $leadData['email'],
            'lead_source' => $leadData['source'],
            'assignee_name' => $leadData['assignee_name'],
        ]);
    }

    public function sendWelcome(array $to, array $userData): bool
    {
        return $this->sendTemplate('welcome', $to, [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'] ?? 'Use password reset to set password',
        ]);
    }

    public function sendBulk(array $recipients, string $templateSlug, array $variables = []): int
    {
        $sent = 0;

        foreach ($recipients as $recipient) {
            if ($this->sendTemplate($templateSlug, $recipient, $variables)) {
                $sent++;
            }
        }

        return $sent;
    }

    public function sendCampaign(string $campaignName, array $recipients, string $templateSlug, array $variables = []): array
    {
        $results = [
            'campaign_name' => $campaignName,
            'template' => $templateSlug,
            'total_recipients' => count($recipients),
            'sent' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($recipients as $index => $recipient) {
            try {
                if ($this->sendTemplate($templateSlug, $recipient, $variables)) {
                    $results['sent']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Failed to send to recipient at index {$index}";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Error sending to recipient at index {$index}: {$e->getMessage()}";
            }
        }

        Log::info('Email campaign completed', $results);

        return $results;
    }

    public function sendScheduledEmail(array $to, string $subject, string $body, \DateTime $scheduledTime, array $attachments = []): bool
    {
        // For now, send immediately. In production, dispatch to queue with delay
        return $this->send($to, $subject, $body, $attachments);
    }

    public function sendReminder(array $to, string $reminderType, array $data): bool
    {
        $templates = [
            'payment_due' => 'payment_reminder',
            'document_deadline' => 'document_reminder',
            'court_date' => 'court_date_reminder',
            'follow_up' => 'follow_up_reminder',
        ];

        $templateSlug = $templates[$reminderType] ?? 'general_reminder';

        return $this->sendTemplate($templateSlug, $to, $data);
    }

    public static function getDefaultTemplates(): array
    {
        return [
            [
                'name' => 'Case Assigned',
                'slug' => 'case_assigned',
                'subject' => 'New Case Assigned - {{case_number}}',
                'body' => '<h2>New Case Assigned</h2><p>Hello,</p><p>You have been assigned a new case:</p><ul><li><strong>Case Number:</strong> {{case_number}}</li><li><strong>Case Title:</strong> {{case_title}}</li><li><strong>Attorney:</strong> {{attorney_name}}</li></ul><p>Please log in to view the case details.</p>',
                'category' => 'case',
                'variables' => ['case_number', 'case_title', 'attorney_name', 'client_name'],
            ],
            [
                'name' => 'Invoice Generated',
                'slug' => 'invoice_generated',
                'subject' => 'Invoice #{{invoice_number}} - Payment Due',
                'body' => '<h2>New Invoice</h2><p>Hello {{client_name}},</p><p>A new invoice has been generated:</p><ul><li><strong>Invoice Number:</strong> {{invoice_number}}</li><li><strong>Amount:</strong> {{amount}}</li><li><strong>Due Date:</strong> {{due_date}}</li></ul><p>Please make the payment by the due date.</p>',
                'category' => 'invoice',
                'variables' => ['invoice_number', 'amount', 'due_date', 'client_name'],
            ],
            [
                'name' => 'Task Assigned',
                'slug' => 'task_assigned',
                'subject' => 'New Task Assigned - {{task_title}}',
                'body' => '<h2>New Task</h2><p>Hello,</p><p>A new task has been assigned to you:</p><ul><li><strong>Task:</strong> {{task_title}}</li><li><strong>Due Date:</strong> {{due_date}}</li><li><strong>Case:</strong> {{case_number}}</li></ul>',
                'category' => 'task',
                'variables' => ['task_title', 'due_date', 'case_number', 'assignee_name'],
            ],
            [
                'name' => 'Welcome Email',
                'slug' => 'welcome',
                'subject' => 'Welcome to LegalFirm',
                'body' => '<h2>Welcome {{name}}!</h2><p>Your account has been created.</p><p>Email: {{email}}</p><p>Please login and reset your password.</p>',
                'category' => 'general',
                'variables' => ['name', 'email', 'password'],
                'is_default' => true,
            ],
            [
                'name' => 'Client Welcome',
                'slug' => 'client_welcome',
                'subject' => 'Welcome to Our Legal Services - {{client_name}}',
                'body' => '<h2>Welcome {{client_name}}!</h2><p>Thank you for choosing our legal services.</p><p>You can access your client portal here: <a href="{{portal_url}}">{{portal_url}}</a></p><p>Next steps: Please upload your documents and complete KYC verification.</p>',
                'category' => 'onboarding',
                'variables' => ['client_name', 'portal_url'],
            ],
            [
                'name' => 'Document Request',
                'slug' => 'document_request',
                'subject' => 'Document Checklist - {{client_name}}',
                'body' => '<h2>Hello {{client_name}},</h2><p>To proceed with your case, please upload the following documents:</p><ul><li>Aadhaar Card</li><li>PAN Card</li><li>Address Proof</li><li>{{required_documents}}</li></ul><p>You can upload these in your client portal.</p>',
                'category' => 'onboarding',
                'variables' => ['client_name', 'required_documents'],
            ],
            [
                'name' => 'Case Feedback Survey',
                'slug' => 'case_feedback',
                'subject' => 'Share Your Feedback - Case {{case_number}}',
                'body' => '<h2>Hello {{client_name}},</h2><p>Thank you for choosing our legal services for case {{case_number}}.</p><p>We value your feedback! Please take a moment to complete our short survey: <a href="{{survey_url}}">{{survey_url}}</a></p><p>Your input helps us improve our services.</p>',
                'category' => 'feedback',
                'variables' => ['client_name', 'case_number', 'survey_url'],
            ],
            [
                'name' => 'Payment Reminder',
                'slug' => 'payment_reminder',
                'subject' => 'Payment Reminder - Invoice {{invoice_number}}',
                'body' => '<h2>Payment Reminder</h2><p>Hello {{client_name}},</p><p>This is a reminder that payment for invoice {{invoice_number}} is due on {{due_date}}.</p><p>Amount: {{amount}}</p><p>Please make the payment to avoid any delays in your case.</p>',
                'category' => 'billing',
                'variables' => ['client_name', 'invoice_number', 'due_date', 'amount'],
            ],
            [
                'name' => 'Document Deadline Reminder',
                'slug' => 'document_reminder',
                'subject' => 'Document Submission Deadline - {{case_number}}',
                'body' => '<h2>Document Reminder</h2><p>Hello {{client_name}},</p><p>Please submit the following documents by {{deadline_date}} to avoid delays in your case {{case_number}}:</p><p>{{required_documents}}</p><p>You can upload documents through your client portal.</p>',
                'category' => 'case',
                'variables' => ['client_name', 'case_number', 'deadline_date', 'required_documents'],
            ],
            [
                'name' => 'Court Date Reminder',
                'slug' => 'court_date_reminder',
                'subject' => 'Court Date Reminder - {{case_number}}',
                'body' => '<h2>Court Date Reminder</h2><p>Hello {{client_name}},</p><p>Your court date for case {{case_number}} is scheduled for:</p><p><strong>Date:</strong> {{court_date}}</p><p><strong>Time:</strong> {{court_time}}</p><p><strong>Location:</strong> {{court_location}}</p><p>Please arrive 30 minutes early.</p>',
                'category' => 'case',
                'variables' => ['client_name', 'case_number', 'court_date', 'court_time', 'court_location'],
            ],
            [
                'name' => 'Follow-up Reminder',
                'slug' => 'follow_up_reminder',
                'subject' => 'Follow-up Required - {{case_number}}',
                'body' => '<h2>Follow-up Reminder</h2><p>Hello {{client_name}},</p><p>This is a reminder regarding your case {{case_number}}.</p><p>{{follow_up_message}}</p><p>Please contact us if you need any assistance.</p>',
                'category' => 'case',
                'variables' => ['client_name', 'case_number', 'follow_up_message'],
            ],
        ];
    }
}
