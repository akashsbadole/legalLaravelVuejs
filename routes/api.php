<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CaseController;
use App\Http\Controllers\Api\Chatbot\ChatbotDocumentCategoryController;
use App\Http\Controllers\Api\Chatbot\ChatbotDocumentController;
use App\Http\Controllers\Api\Chatbot\ChatbotDocumentSearchController;
use App\Http\Controllers\Api\Legal\ChatbotController;
use App\Http\Controllers\Api\Legal\ConstitutionController;
use App\Http\Controllers\Api\Legal\CpcController;
use App\Http\Controllers\Api\Legal\CrpcController;
use App\Http\Controllers\Api\Legal\IpcController;
use App\Http\Controllers\Api\Legal\SearchController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\ClientPortalController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TrustAccountController;
use App\Http\Controllers\CommunicationHealthController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TwilioWebhookController;
use App\Models\CaseStatus;
use App\Models\CaseTask;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\LegalCase;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::prefix('tenant/{tenant}')->middleware(['auth:sanctum', 'audit', 'set.tenant'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::put('/user', function (Request $request) {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.auth()->id(),
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        auth()->user()->update($data);

        return auth()->user();
    });

    Route::put('/user/password', function (Request $request) {
        $data = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (! Hash::check($data['current_password'], auth()->user()->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 403);
        }

        auth()->user()->update(['password' => Hash::make($data['password'])]);

        return response()->json(['message' => 'Password updated successfully']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard Stats
    Route::get('/dashboard/stats', function () {
        return response()->json([
            'cases' => LegalCase::count(),
            'clients' => User::where('role', 'client')->count(),
            'pendingInvoices' => Invoice::where('status', 'pending')->count(),
            'tasksDue' => CaseTask::whereNull('completed_at')->where('due_date', '<=', now()->addDays(7))->count(),
            'users' => User::count(),
            'revenue' => Invoice::where('status', 'paid')->sum('total'),
        ]);
    });

    // Case Statuses
    Route::get('/case-statuses', function () {
        return CaseStatus::all();
    });

    // Cases CRUD
    Route::get('/cases', [CaseController::class, 'index']);
    Route::get('/cases/{case}', [CaseController::class, 'show']);
    Route::post('/cases', [CaseController::class, 'store']);
    Route::put('/cases/{case}', [CaseController::class, 'update']);
    Route::delete('/cases/{case}', [CaseController::class, 'destroy']);

    // Clients CRUD
    Route::get('/clients', function () {
        return User::where('role', 'client')->paginate(20);
    });
    Route::get('/clients/all', function () {
        return User::where('role', 'client')->get();
    });
    Route::get('/clients/{user}', function (User $user) {
        return $user;
    });
    Route::post('/clients', function (Request $request) {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        $data['password'] = bcrypt('password');
        $data['role'] = 'client';

        return User::create($data);
    });
    Route::put('/clients/{user}', function (Request $request, User $user) {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        $user->update($data);

        return $user;
    });
    Route::delete('/clients/{user}', function (User $user) {
        $user->delete();
        return response()->json(['message' => 'Client deleted']);
    });

    // Invoices
    Route::get('/invoices', function () {
        return Invoice::with('client')->latest()->paginate(20);
    });
    Route::get('/invoices/{invoice}', function (Invoice $invoice) {
        return $invoice->load('client', 'items');
    });
    Route::post('/invoices', function (Request $request) {
        $data = $request->validate([
            'client_id' => 'required|exists:users,id',
            'total' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'issue_date' => 'nullable|date',
            'description' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        $data['invoice_number'] = 'INV-'.date('Ymd').'-'.rand(1000, 9999);
        $data['status'] = $data['status'] ?? 'pending';
        $data['created_by'] = auth()->id();
        $data['subtotal'] = $data['total'];
        $data['total_amount'] = $data['total'];
        $data['issue_date'] = $data['issue_date'] ?? now()->toDateString();
        $data['notes'] = $data['description'] ?? '';

        return Invoice::create($data);
    });
    Route::post('/invoices/{invoice}/payment', function (Invoice $invoice) {
        $invoice->update(['status' => 'paid', 'paid_date' => now()]);
        return $invoice;
    });
    Route::put('/invoices/{invoice}', function (Request $request, Invoice $invoice) {
        $data = $request->validate([
            'client_id' => 'sometimes|exists:users,id',
            'total' => 'sometimes|numeric|min:0',
            'due_date' => 'sometimes|date',
            'issue_date' => 'nullable|date',
            'description' => 'nullable|string',
            'status' => 'sometimes|string',
        ]);
        $invoice->update($data);
        return $invoice;
    });
    Route::delete('/invoices/{invoice}', function (Invoice $invoice) {
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted']);
    });

    // Tasks
    Route::get('/tasks', function (Request $request) {
        $query = CaseTask::with('case');
        if ($request->has('upcoming')) {
            $query->whereNull('completed_at')->where('due_date', '<=', now()->addDays(7));
        }
        if ($request->has('case_id')) {
            $query->where('case_id', $request->case_id);
        }

        return $query->latest()->get();
    });
    Route::post('/tasks', function (Request $request) {
        $data = $request->validate([
            'title' => 'required|string',
            'case_id' => 'nullable|exists:cases,id',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $data['assigned_by'] = auth()->id();

        return CaseTask::create($data);
    });
    Route::patch('/tasks/{task}', function (Request $request, CaseTask $task) {
        $task->update($request->all());

        return $task;
    });
    Route::delete('/tasks/{task}', function (CaseTask $task) {
        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    });

    // Leads
    Route::get('/leads', function () {
        return Lead::with('assignedTo')->latest()->get();
    });
    Route::post('/leads', function (Request $request) {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
            'status' => 'nullable|string',
            'source' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        return Lead::create($data);
    });
    Route::put('/leads/{lead}', function (Request $request, Lead $lead) {
        $lead->update($request->all());

        return $lead;
    });
    Route::delete('/leads/{lead}', function (Lead $lead) {
        $lead->delete();

        return response()->json(['message' => 'Lead deleted']);
    });

    // Documents - using Document model
    Route::get('/documents', function (Request $request) {
        $query = \App\Models\Document::with(['category', 'createdBy', 'case']);
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        return $query->latest()->paginate(20);
    });
    Route::get('/documents/all', function () {
        return \App\Models\Document::with(['category'])->latest()->get();
    });
    Route::post('/documents', function (Request $request) {
        $data = $request->validate([
            'name' => 'required|string',
            'file_path' => 'nullable|string',
            'category_id' => 'nullable|exists:document_categories,id',
            'case_id' => 'nullable|exists:legal_cases,id',
            'is_generated' => 'nullable|boolean',
        ]);
        $data['created_by'] = auth()->id();
        
        return \App\Models\Document::create($data);
    });
    Route::put('/documents/{document}', function (Request $request, \App\Models\Document $document) {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'category_id' => 'nullable|exists:document_categories,id',
            'case_id' => 'nullable|exists:legal_cases,id',
        ]);
        $document->update($data);
        return $document;
    });
    Route::delete('/documents/{document}', function (\App\Models\Document $document) {
        $document->delete();
        return response()->json(['message' => 'Document deleted']);
    });

    // Expenses
    Route::get('/expenses', function (Request $request) {
        $query = \App\Models\Expense::with('category');
        if ($request->has('case_id')) {
            $query->where('case_id', $request->case_id);
        }
        return $query->latest()->paginate(20);
    });
    Route::post('/expenses', function (Request $request) {
        $data = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:expense_categories,id',
            'case_id' => 'nullable|exists:legal_cases,id',
            'date' => 'nullable|date',
        ]);
        $data['created_by'] = auth()->id();
        return \App\Models\Expense::create($data);
    });
    Route::put('/expenses/{expense}', function (Request $request, \App\Models\Expense $expense) {
        $data = $request->validate([
            'description' => 'sometimes|string',
            'amount' => 'sometimes|numeric|min:0',
            'category_id' => 'nullable|exists:expense_categories,id',
            'case_id' => 'nullable|exists:legal_cases,id',
            'date' => 'nullable|date',
        ]);
        $expense->update($data);
        return $expense;
    });
    Route::delete('/expenses/{expense}', function (\App\Models\Expense $expense) {
        $expense->delete();
        return response()->json(['message' => 'Expense deleted']);
    });

    // Time Entries
    Route::get('/time-entries', function (Request $request) {
        $query = \App\Models\TimeEntry::with('case');
        if ($request->has('case_id')) {
            $query->where('case_id', $request->case_id);
        }
        return $query->latest()->paginate(20);
    });
    Route::post('/time-entries', function (Request $request) {
        $data = $request->validate([
            'description' => 'required|string',
            'hours' => 'required|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'case_id' => 'nullable|exists:legal_cases,id',
            'date' => 'nullable|date',
        ]);
        $data['user_id'] = auth()->id();
        return \App\Models\TimeEntry::create($data);
    });
    Route::put('/time-entries/{timeEntry}', function (Request $request, \App\Models\TimeEntry $timeEntry) {
        $data = $request->validate([
            'description' => 'sometimes|string',
            'hours' => 'sometimes|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'case_id' => 'nullable|exists:legal_cases,id',
            'date' => 'nullable|date',
        ]);
        $timeEntry->update($data);
        return $timeEntry;
    });
    Route::delete('/time-entries/{timeEntry}', function (\App\Models\TimeEntry $timeEntry) {
        $timeEntry->delete();
        return response()->json(['message' => 'Time entry deleted']);
    });

    // Expense Categories
    Route::get('/expense-categories', function () {
        return \App\Models\ExpenseCategory::all();
    });

    // Calendar Events CRUD
    Route::get('/calendar/events', [CalendarController::class, 'index']);
    Route::post('/calendar/events', [CalendarController::class, 'store']);
    Route::get('/calendar/events/{calendar}', [CalendarController::class, 'show']);
    Route::put('/calendar/events/{calendar}', [CalendarController::class, 'update']);
    Route::delete('/calendar/events/{calendar}', [CalendarController::class, 'destroy']);

    // Notifications CRUD
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::get('/notifications/{notification}', [NotificationController::class, 'show']);
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications', [NotificationController::class, 'destroyAll']);

    // Trust Accounts CRUD
    Route::get('/trust-accounts', [TrustAccountController::class, 'index']);
    Route::post('/trust-accounts', [TrustAccountController::class, 'store']);

    // Trust Account Stats
    Route::get('/trust-accounts/stats', [TrustAccountController::class, 'stats']);

    // Client Trust Account Balance
    Route::get('/clients/{client}/trust-balance', [TrustAccountController::class, 'clientBalance']);

    // Specific trust account routes (must come after specific routes)
    Route::get('/trust-accounts/{trustAccount}', [TrustAccountController::class, 'show']);
    Route::put('/trust-accounts/{trustAccount}', [TrustAccountController::class, 'update']);
    Route::delete('/trust-accounts/{trustAccount}', [TrustAccountController::class, 'destroy']);

    // Notifications
    Route::get('/notifications', function () {
        return response()->json([
            ['id' => 1, 'type' => 'case', 'title' => 'New Case Assigned', 'message' => 'Case "Smith vs. Johnson" has been assigned to you.', 'read' => false, 'created_at' => now()->subMinutes(5)->toISOString()],
            ['id' => 2, 'type' => 'task', 'title' => 'Task Due Tomorrow', 'message' => '"Review contract draft" is due tomorrow.', 'read' => false, 'created_at' => now()->subHours(1)->toISOString()],
            ['id' => 3, 'type' => 'billing', 'title' => 'Invoice Payment Received', 'message' => 'Payment of $5,000 received.', 'read' => true, 'created_at' => now()->subDays(1)->toISOString()],
        ]);
    });

    // Search
    Route::get('/search', function (Request $request) {
        $q = $request->query('q', '');
        $results = [];

        if (strlen($q) >= 2) {
            // Search cases
            $cases = LegalCase::where('title', 'like', "%{$q}%")->limit(5)->get();
            foreach ($cases as $case) {
                $results[] = ['id' => $case->id, 'type' => 'case', 'title' => $case->title, 'description' => $case->case_number, 'date' => $case->created_at->toISOString()];
            }

            // Search clients
            $clients = User::where('role', 'client')->where('name', 'like', "%{$q}%")->limit(5)->get();
            foreach ($clients as $client) {
                $results[] = ['id' => $client->id, 'type' => 'client', 'title' => $client->name, 'description' => $client->email, 'date' => $client->created_at->toISOString()];
            }
        }

        return response()->json($results);
    });

    // CRM Interactions
    Route::get('/crm/interactions', function (Request $request) {
        $query = \App\Models\InteractionLog::with('client');
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        return $query->latest()->paginate(20);
    });
    Route::post('/crm/interactions', function (Request $request) {
        $data = $request->validate([
            'client_id' => 'required|exists:users,id',
            'type' => 'required|string|in:call,email,meeting,note',
            'subject' => 'required|string',
            'description' => 'nullable|string',
            'direction' => 'nullable|string|in:inbound,outbound',
            'status' => 'nullable|string|in:scheduled,completed,cancelled',
            'scheduled_at' => 'nullable|date',
        ]);
        $data['created_by'] = auth()->id();
        return \App\Models\InteractionLog::create($data);
    });
    Route::put('/crm/interactions/{interaction}', function (Request $request, \App\Models\InteractionLog $interaction) {
        $data = $request->validate([
            'type' => 'sometimes|string|in:call,email,meeting,note',
            'subject' => 'sometimes|string',
            'description' => 'nullable|string',
            'direction' => 'nullable|string|in:inbound,outbound',
            'status' => 'nullable|string|in:scheduled,completed,cancelled',
            'scheduled_at' => 'nullable|date',
        ]);
        $interaction->update($data);
        return $interaction;
    });
    Route::delete('/crm/interactions/{interaction}', function (\App\Models\InteractionLog $interaction) {
        $interaction->delete();
        return response()->json(['message' => 'Interaction deleted']);
    });

    // Follow-ups
    Route::get('/crm/follow-ups', function (Request $request) {
        $query = \App\Models\FollowUp::with(['client', 'assignedTo']);
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        return $query->latest()->paginate(20);
    });
    Route::post('/crm/follow-ups', function (Request $request) {
        $data = $request->validate([
            'client_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'nullable|string|in:low,medium,high',
            'status' => 'nullable|string|in:pending,completed,cancelled',
        ]);
        $data['created_by'] = auth()->id();
        return \App\Models\FollowUp::create($data);
    });
    Route::put('/crm/follow-ups/{followUp}', function (Request $request, \App\Models\FollowUp $followUp) {
        $data = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'due_date' => 'sometimes|date',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'nullable|string|in:low,medium,high',
            'status' => 'nullable|string|in:pending,completed,cancelled',
        ]);
        $followUp->update($data);
        return $followUp;
    });
    Route::delete('/crm/follow-ups/{followUp}', function (\App\Models\FollowUp $followUp) {
        $followUp->delete();
        return response()->json(['message' => 'Follow-up deleted']);
    });
});

// Auth Routes
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/register', [AuthController::class, 'register']);

// Session token route for web-to-API token bridging (outside tenant group)
Route::middleware('web')->group(function () {
    Route::get('/session-token', function (Request $request) {
        if (auth()->check() && session()->has('api_token')) {
            $token = session()->get('api_token');
            session()->forget('api_token');

            return response()->json(['token' => $token]);
        }

        return response()->json(['token' => null], 404);
    });
});

// User Route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Settings Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/settings', function () {
        $settings = Setting::all()->pluck('value', 'key');
        $settings['currency_symbol'] = match ($settings['currency'] ?? 'USD') {
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => '$'
        };

        return response()->json($settings);
    });

    Route::put('/settings', function (Request $request) {
        foreach ($request->all() as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json(['message' => 'Settings updated']);
    });

    // Reports Routes
    Route::get('/reports', [ReportsController::class, 'index']);
    Route::get('/reports/financial', [ReportsController::class, 'financial']);
    Route::get('/reports/cases', [ReportsController::class, 'cases']);
    Route::get('/reports/export', [ReportsController::class, 'export']);
});

// Legal Database API Routes
Route::prefix('legal')->group(function () {
    // IPC routes
    Route::get('/ipc/search', [IpcController::class, 'search']);
    Route::apiResource('/ipc', IpcController::class)->only(['index', 'show']);

    // CrPC routes
    Route::get('/crpc/search', [CrpcController::class, 'search']);
    Route::apiResource('/crpc', CrpcController::class)->only(['index', 'show']);

    // CPC routes
    Route::get('/cpc/search', [CpcController::class, 'search']);
    Route::apiResource('/cpc', CpcController::class)->only(['index', 'show']);

    // Constitution routes
    Route::get('/constitution/search', [ConstitutionController::class, 'search']);
    Route::get('/constitution/category/{category}', [ConstitutionController::class, 'byCategory']);
    Route::get('/constitution/part/{part}', [ConstitutionController::class, 'byPart']);
    Route::apiResource('/constitution', ConstitutionController::class)->only(['index', 'show']);

    // Global search
    Route::get('/search', [SearchController::class, 'search']);
    Route::post('/search/advanced', [SearchController::class, 'advanced']);
});

// Legal Chatbot Conversation API Routes
Route::prefix('legal/chatbot')->group(function () {
    Route::post('/conversation', [ChatbotController::class, 'conversation']);
    Route::get('/conversations', [ChatbotController::class, 'conversations']);
    Route::get('/conversation/{conversation}', [ChatbotController::class, 'showConversation']);
    Route::put('/conversation/{conversation}', [ChatbotController::class, 'updateConversation']);
    Route::delete('/conversation/{conversation}', [ChatbotController::class, 'destroyConversation']);
    Route::post('/message', [ChatbotController::class, 'message']);
});

// Chatbot Document Management API Routes
Route::prefix('chatbot/documents')->group(function () {
    // Document CRUD
    Route::get('/', [ChatbotDocumentController::class, 'index']);
    Route::post('/', [ChatbotDocumentController::class, 'store']);
    Route::get('/stats', [ChatbotDocumentController::class, 'stats']);
    Route::get('/{document}', [ChatbotDocumentController::class, 'show']);
    Route::put('/{document}', [ChatbotDocumentController::class, 'update']);
    Route::delete('/{document}', [ChatbotDocumentController::class, 'destroy']);
    Route::get('/{document}/download', [ChatbotDocumentController::class, 'download']);

    // Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [ChatbotDocumentCategoryController::class, 'index']);
        Route::post('/', [ChatbotDocumentCategoryController::class, 'store']);
        Route::get('/tree', [ChatbotDocumentCategoryController::class, 'tree']);
        Route::get('/{category}', [ChatbotDocumentCategoryController::class, 'show']);
        Route::put('/{category}', [ChatbotDocumentCategoryController::class, 'update']);
        Route::delete('/{category}', [ChatbotDocumentCategoryController::class, 'destroy']);
        Route::get('/{category}/documents', [ChatbotDocumentCategoryController::class, 'documents']);
        Route::get('/{category}/stats', [ChatbotDocumentCategoryController::class, 'stats']);
        Route::post('/bulk-update', [ChatbotDocumentCategoryController::class, 'bulkUpdate']);
    });

    // Search
    Route::prefix('search')->group(function () {
        Route::get('/', [ChatbotDocumentSearchController::class, 'search']);
        Route::get('/suggestions', [ChatbotDocumentSearchController::class, 'suggestions']);
        Route::get('/filters', [ChatbotDocumentSearchController::class, 'filters']);
        Route::get('/analytics', [ChatbotDocumentSearchController::class, 'analytics']);
        Route::get('/export', [ChatbotDocumentSearchController::class, 'export']);
    });
});

// Client Portal API Routes
Route::middleware('auth:sanctum')->prefix('portal')->group(function () {
    Route::get('/dashboard', [ClientPortalController::class, 'dashboard']);
    Route::get('/cases', [ClientPortalController::class, 'cases']);
    Route::get('/invoices', [ClientPortalController::class, 'invoices']);
    Route::get('/documents', [ClientPortalController::class, 'documents']);
});

// Admin API Routes
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/users', [AdminController::class, 'users']);
    Route::put('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus']);
    Route::get('/audit-logs', [AdminController::class, 'auditLogs']);
});

// Webhook routes (no auth required)
Route::middleware('rate-limit-webhooks')->group(function () {
    Route::post('/webhooks/twilio/whatsapp', [TwilioWebhookController::class, 'handleWhatsApp']);
    Route::post('/webhooks/twilio/sms', [TwilioWebhookController::class, 'handleSms']);
});

// Health check endpoints (public)
Route::get('/health/communication', [CommunicationHealthController::class, 'health']);
