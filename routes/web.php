<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TrustAccountController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/otp/login', [App\Http\Controllers\Auth\OtpLoginController::class, 'showLoginForm'])->name('otp.login');
Route::post('/otp/send', [App\Http\Controllers\Auth\OtpLoginController::class, 'sendOtp'])->name('otp.send');
Route::get('/otp/verify', [App\Http\Controllers\Auth\OtpLoginController::class, 'showVerifyForm'])->name('otp.verify');
Route::post('/otp/verify', [App\Http\Controllers\Auth\OtpLoginController::class, 'verifyOtp'])->name('otp.verify.post');
Route::post('/otp/resend', [App\Http\Controllers\Auth\OtpLoginController::class, 'resendOtp'])->name('otp.resend');

Route::get('/dashboard', function () {
    return redirect()->route('cases.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');

    Route::get('crm', [App\Http\Controllers\CrmController::class, 'dashboard'])->name('crm.dashboard');

    Route::resource('cases', CaseController::class);

    Route::post('cases/{case}/notes', [CaseController::class, 'addNote'])->name('cases.notes.store');
    Route::post('cases/{case}/participants', [CaseController::class, 'addParticipant'])->name('cases.participants.store');
    Route::delete('cases/{case}/participants/{participant}', [CaseController::class, 'removeParticipant'])->name('cases.participants.destroy');
    Route::get('cases/{case}/timeline', [CaseController::class, 'timeline'])->name('cases.timeline');
    Route::post('cases/{case}/timeline', [CaseController::class, 'storeTimelineEvent'])->name('cases.timeline.store');
    Route::delete('timeline/{event}', [CaseController::class, 'destroyTimelineEvent'])->name('cases.timeline.destroy');

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
        Route::get('invoices', [App\Http\Controllers\ClientPortalController::class, 'invoices'])->name('invoices');
        Route::get('invoices/{invoice}', [App\Http\Controllers\ClientPortalController::class, 'invoiceDetail'])->name('invoices.show');
        Route::post('invoices/{invoice}/pay', [App\Http\Controllers\ClientPortalController::class, 'payInvoice'])->name('invoices.pay');
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
    Route::get('reports/export', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
});

Route::get('/test-admin', function () {
    if (auth()->check() && auth()->user()->role === 'admin') {
        return 'Admin access granted! Go to <a href="/admin/settings">/admin/settings</a>';
    }
    return 'Not admin or not logged in.';
})->middleware('auth');

require __DIR__.'/auth.php';