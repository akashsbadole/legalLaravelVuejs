<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TrustAccountController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/features', function () {
    return view('landing');
})->name('features');

// Health check endpoint
Route::get('/health', function () {
    try {
        // Check database connection
        DB::connection()->getPdo();

        // Check key tables exist
        $tablesExist = Schema::hasTable('users') &&
                      Schema::hasTable('cases') &&
                      Schema::hasTable('clients');

        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'services' => [
                'database' => 'connected',
                'cache' => cache()->store()->getStore() ? 'connected' : 'disconnected',
                'storage' => is_writable(storage_path()) ? 'writable' : 'not writable',
            ],
            'tables' => $tablesExist ? 'exist' : 'missing',
            'version' => config('app.version', '1.0.0'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'error' => $e->getMessage(),
            'timestamp' => now()->toISOString(),
        ], 500);
    }
})->name('health');

Route::get('/otp/login', [App\Http\Controllers\Auth\OtpLoginController::class, 'showLoginForm'])->name('otp.login');
Route::post('/otp/send', [App\Http\Controllers\Auth\OtpLoginController::class, 'sendOtp'])->name('otp.send');
Route::get('/otp/verify', [App\Http\Controllers\Auth\OtpLoginController::class, 'showVerifyForm'])->name('otp.verify');
Route::post('/otp/verify', [App\Http\Controllers\Auth\OtpLoginController::class, 'verifyOtp'])->name('otp.verify.post');
Route::post('/otp/resend', [App\Http\Controllers\Auth\OtpLoginController::class, 'resendOtp'])->name('otp.resend');

Route::get('/dashboard', function () {
    return redirect()->route('cases.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'two-factor'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');

    Route::get('crm', [App\Http\Controllers\CrmController::class, 'dashboard'])->name('crm.dashboard');
    Route::get('crm/clients/{client}/interactions/create', [App\Http\Controllers\CrmController::class, 'createInteraction'])->name('crm.interactions.create');
    Route::post('crm/interaction', [App\Http\Controllers\CrmController::class, 'logInteraction'])->name('crm.log-interaction');
    Route::get('crm/clients/{client}/interactions', [App\Http\Controllers\CrmController::class, 'clientInteractions'])->name('crm.client-interactions');
    Route::get('crm/clients/{client}/follow-ups/create', [App\Http\Controllers\CrmController::class, 'createFollowUpForm'])->name('crm.follow-ups.create');
    Route::post('crm/follow-up', [App\Http\Controllers\CrmController::class, 'createFollowUp'])->name('crm.create-follow-up');

    Route::resource('cases', CaseController::class);

    Route::post('cases/{case}/notes', [CaseController::class, 'addNote'])->name('cases.notes.store');
    Route::post('cases/{case}/participants', [CaseController::class, 'addParticipant'])->name('cases.participants.store');
    Route::delete('cases/{case}/participants/{participant}', [CaseController::class, 'removeParticipant'])->name('cases.participants.destroy');
    Route::get('cases/{case}/timeline', [CaseController::class, 'timeline'])->name('cases.timeline');
    Route::post('cases/{case}/timeline', [CaseController::class, 'storeTimelineEvent'])->name('cases.timeline.store');
    Route::delete('timeline/{event}', [CaseController::class, 'destroyTimelineEvent'])->name('cases.timeline.destroy');
    Route::post('cases/{case}/follow-ups', [CaseController::class, 'createFollowUp'])->name('cases.followUps.store');
    Route::put('follow-ups/{followUp}', [CaseController::class, 'updateFollowUp'])->name('cases.followUps.update');
    Route::delete('follow-ups/{followUp}', [CaseController::class, 'destroyFollowUp'])->name('cases.followUps.destroy');
    Route::post('cases/{case}/calls', [CaseController::class, 'logCall'])->name('cases.calls.store');
    Route::post('cases/{case}/assign-staff', [CaseController::class, 'assignStaff'])->name('cases.assignStaff');

    Route::get('calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('calendar/events', [App\Http\Controllers\CalendarController::class, 'getEvents'])->name('calendar.events');
    Route::post('calendar', [App\Http\Controllers\CalendarController::class, 'store'])->name('calendar.store');

    Route::resource('leads', App\Http\Controllers\LeadController::class);
    Route::post('leads/{lead}/activities', [App\Http\Controllers\LeadController::class, 'addActivity'])->name('leads.activities.store');
    Route::post('leads/{lead}/convert', [App\Http\Controllers\LeadController::class, 'convertToCase'])->name('leads.convert');

    Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('documents/templates', [DocumentController::class, 'templates'])->name('documents.templates');
    Route::get('documents/templates/create', [DocumentController::class, 'createTemplate'])->name('documents.templates.create');
    Route::post('documents/templates', [DocumentController::class, 'storeTemplate'])->name('documents.templates.store');
    Route::get('documents/templates/{template}/edit', [DocumentController::class, 'editTemplate'])->name('documents.templates.edit');
    Route::put('documents/templates/{template}', [DocumentController::class, 'updateTemplate'])->name('documents.templates.update');
    Route::get('documents/templates/{template}/preview', [DocumentController::class, 'previewTemplate'])->name('documents.templates.preview');
    Route::get('documents/generate', [DocumentController::class, 'generate'])->name('documents.generate');
    Route::post('documents/generate', [DocumentController::class, 'generateDocument'])->name('documents.generate.store');
    Route::get('documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');

    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('time-entries', [BillingController::class, 'timeEntries'])->name('time-entries');
        Route::post('time-entries', [BillingController::class, 'storeTimeEntry'])->name('time-entries.store');
        Route::put('time-entries/{timeEntry}', [BillingController::class, 'updateTimeEntry'])->name('time-entries.update');
        Route::post('time-entries/{timeEntry}/approve', [BillingController::class, 'approveTimeEntry'])->name('time-entries.approve');
        Route::post('time-entries/{timeEntry}/reject', [BillingController::class, 'rejectTimeEntry'])->name('time-entries.reject');

        Route::get('expenses', [BillingController::class, 'expenses'])->name('expenses');
        Route::post('expenses', [BillingController::class, 'storeExpense'])->name('expenses.store');

        Route::get('invoices', [BillingController::class, 'invoices'])->name('invoices');
        Route::get('invoices/create', [BillingController::class, 'createInvoice'])->name('invoices.create');
        Route::post('invoices', [BillingController::class, 'storeInvoice'])->name('invoices.store');
        Route::get('invoices/{invoice}', [BillingController::class, 'showInvoice'])->name('invoices.show');
        Route::post('invoices/{invoice}/send', [BillingController::class, 'sendInvoice'])->name('invoices.send');
        Route::post('invoices/{invoice}/paid', [BillingController::class, 'markPaid'])->name('invoices.paid');

        Route::get('reports', [BillingController::class, 'reports'])->name('reports');
    });

    Route::resource('tasks', TaskController::class);
    Route::get('my-tasks', [TaskController::class, 'myTasks'])->name('tasks.my');
    Route::get('cases/{case}/tasks', [TaskController::class, 'caseTasks'])->name('cases.tasks');
    Route::post('tasks/{task}/comments', [App\Http\Controllers\TaskController::class, 'storeComment'])->name('tasks.comments.store');

    Route::prefix('portal')->name('portal.')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\ClientPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('cases', [App\Http\Controllers\ClientPortalController::class, 'cases'])->name('cases');
        Route::get('cases/{case}', [App\Http\Controllers\ClientPortalController::class, 'caseDetail'])->name('cases.show');
        Route::post('cases/{case}/message', [App\Http\Controllers\ClientPortalController::class, 'sendMessage'])->name('cases.message');
        Route::get('documents', [App\Http\Controllers\ClientPortalController::class, 'documents'])->name('documents');
        Route::get('documents/{document}/download', [App\Http\Controllers\ClientPortalController::class, 'downloadDocument'])->name('documents.download');
        Route::get('onboarding', [App\Http\Controllers\ClientPortalController::class, 'onboarding'])->name('onboarding');
        Route::get('survey/{survey}', [App\Http\Controllers\ClientPortalController::class, 'survey'])->name('survey');
        Route::post('survey/{survey}', [App\Http\Controllers\ClientPortalController::class, 'submitSurvey'])->name('survey.submit');
        Route::get('invoices', [App\Http\Controllers\ClientPortalController::class, 'invoices'])->name('invoices');
        Route::get('invoices/{invoice}', [App\Http\Controllers\ClientPortalController::class, 'invoiceDetail'])->name('invoices.show');
        Route::post('invoices/{invoice}/pay', [App\Http\Controllers\ClientPortalController::class, 'payInvoice'])->name('invoices.pay');
        Route::get('privacy', [App\Http\Controllers\ClientPortalController::class, 'privacy'])->name('privacy');
        Route::post('privacy/export', [App\Http\Controllers\ClientPortalController::class, 'exportData'])->name('privacy.export');
        Route::post('privacy/delete', [App\Http\Controllers\ClientPortalController::class, 'deleteData'])->name('privacy.delete');
        Route::post('privacy/consent', [App\Http\Controllers\ClientPortalController::class, 'updateConsent'])->name('privacy.consent');
    });

    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread', [App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    Route::get('export/cases', [App\Http\Controllers\ExportController::class, 'exportCases'])->name('export.cases');
    Route::get('export/invoices', [App\Http\Controllers\ExportController::class, 'exportInvoices'])->name('export.invoices');
    Route::get('export/time-entries', [App\Http\Controllers\ExportController::class, 'exportTimeEntries'])->name('export.time-entries');
    Route::get('export/expenses', [App\Http\Controllers\ExportController::class, 'exportExpenses'])->name('export.expenses');

    Route::resource('trust-accounts', TrustAccountController::class);
    Route::get('trust-accounts/client/{client}', [TrustAccountController::class, 'clientBalance'])->name('trust-accounts.client');

    Route::get('two-factor/setup', [TwoFactorController::class, 'show'])->name('two-factor.setup');
    Route::post('two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::post('two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
    Route::post('two-factor/regenerate-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.regenerate-codes');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('tenants', App\Http\Controllers\TenantController::class);
        Route::post('tenants/{tenant}/suspend', [App\Http\Controllers\TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::post('tenants/{tenant}/activate', [App\Http\Controllers\TenantController::class, 'activate'])->name('tenants.activate');
        Route::post('tenants/{tenant}/extend', [App\Http\Controllers\TenantController::class, 'extendSubscription'])->name('tenants.extend');
        
        Route::resource('settings', App\Http\Controllers\Admin\SettingsController::class)->except(['show']);
        
        Route::get('audit-logs', [App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('audit-logs/{auditLog}', [App\Http\Controllers\AuditLogController::class, 'show'])->name('audit-logs.show');
        Route::get('audit-logs/export', [App\Http\Controllers\AuditLogController::class, 'export'])->name('audit-logs.export');
    });

    Route::get('reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/cases', [App\Http\Controllers\ReportController::class, 'cases'])->name('reports.cases');
    Route::get('reports/financials', [App\Http\Controllers\ReportController::class, 'financials'])->name('reports.financials');
    Route::get('reports/time-tracking', [App\Http\Controllers\ReportController::class, 'timeTracking'])->name('reports.time-tracking');
    Route::get('reports/attorney-performance', [App\Http\Controllers\ReportController::class, 'attorneyPerformance'])->name('reports.attorney-performance');
    Route::get('reports/client-analytics', [App\Http\Controllers\ReportController::class, 'clientAnalytics'])->name('reports.client-analytics');
    Route::get('reports/client-activity', [App\Http\Controllers\ReportController::class, 'clientActivity'])->name('reports.client-activity');
    Route::get('reports/export', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    Route::post('language/switch', function(\Illuminate\Http\Request $request) {
        $locale = $request->input('locale', 'en');
        if (in_array($locale, ['en', 'hi'])) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }
        return back();
    })->name('language.switch');

    // Admin Onboarding Routes
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('onboarding', [App\Http\Controllers\AdminOnboardingController::class, 'index'])->name('onboarding');
        Route::post('onboarding/next', [App\Http\Controllers\AdminOnboardingController::class, 'next'])->name('onboarding.next');
        Route::post('onboarding/skip', [App\Http\Controllers\AdminOnboardingController::class, 'skip'])->name('onboarding.skip');
    });

    // Contact & Enquiry Routes
    Route::post('contact', [App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');

    Route::prefix('admin/enquiries')->name('admin.enquiries.')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/', [App\Http\Controllers\ContactController::class, 'index'])->name('index');
        Route::get('/{enquiry}', [App\Http\Controllers\ContactController::class, 'show'])->name('show');
        Route::post('/{enquiry}/assign', [App\Http\Controllers\ContactController::class, 'assign'])->name('assign');
        Route::post('/{enquiry}/status', [App\Http\Controllers\ContactController::class, 'updateStatus'])->name('updateStatus');
    });
});

Route::get('/test-admin', function () {
    if (Auth::check() && Auth::user()->role === 'admin') {
        return 'Admin access granted! Go to <a href="/admin/settings">/admin/settings</a>';
    }
    return 'Not admin or not logged in.';
})->middleware('auth');

require __DIR__.'/auth.php';