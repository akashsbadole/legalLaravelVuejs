<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use App\Models\LegalCase;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function timeEntries(Request $request): View
    {
        $query = TimeEntry::with(['case', 'user', 'approvedBy']);

        if ($request->filled('case')) {
            $query->where('case_id', $request->case);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        $entries = $query->orderBy('work_date', 'desc')->paginate(15);
        $cases = LegalCase::all();
        $users = User::whereIn('role', ['attorney', 'paralegal'])->get();

        return view('billing.time-entries', compact('entries', 'cases', 'users'));
    }

    public function storeTimeEntry(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'description' => 'required|string',
            'hours' => 'required|numeric|min:0.1',
            'work_date' => 'required|date',
        ]);

        $user = auth()->user();
        $validated['user_id'] = $user->id;
        $validated['rate'] = $user->hourly_rate ?? 0;
        $validated['status'] = 'draft';

        TimeEntry::create($validated);

        return back()->with('success', 'Time entry added successfully.');
    }

    public function updateTimeEntry(Request $request, TimeEntry $timeEntry): RedirectResponse
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'hours' => 'required|numeric|min:0.1',
            'work_date' => 'required|date',
        ]);

        $timeEntry->update($validated);

        return back()->with('success', 'Time entry updated successfully.');
    }

    public function approveTimeEntry(TimeEntry $timeEntry): RedirectResponse
    {
        $timeEntry->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Time entry approved.');
    }

    public function rejectTimeEntry(Request $request, TimeEntry $timeEntry): RedirectResponse
    {
        $timeEntry->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('info', 'Time entry rejected.');
    }

    public function expenses(Request $request): View
    {
        $query = Expense::with(['case', 'user', 'category']);

        if ($request->filled('case')) {
            $query->where('case_id', $request->case);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15);
        $cases = LegalCase::all();

        return view('billing.expenses', compact('expenses', 'cases'));
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'is_billable' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'draft';

        Expense::create($validated);

        return back()->with('success', 'Expense added successfully.');
    }

    public function invoices(Request $request): View
    {
        $query = Invoice::with(['case', 'client', 'createdBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);
        $clients = User::where('role', 'client')->get();

        return view('billing.invoices', compact('invoices', 'clients'));
    }

    public function createInvoice(Request $request): View
    {
        $cases = LegalCase::with(['client', 'timeEntries' => function ($q) {
            $q->where('status', 'approved');
        }, 'expenses' => function ($q) {
            $q->where('status', 'approved');
        }])->get();

        return view('billing.create-invoice', compact('cases'));
    }

    public function storeInvoice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'case_id' => 'nullable|exists:cases,id',
            'client_id' => 'required|exists:users,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'time_entries' => 'array',
            'expenses' => 'array',
        ]);

        $case = isset($validated['case_id']) ? LegalCase::findOrFail($validated['case_id']) : null;

        $subtotal = 0;
        $lineItems = [];

        if (! empty($validated['time_entries'])) {
            $timeEntries = TimeEntry::whereIn('id', $validated['time_entries'])->where('status', 'approved')->get();
            foreach ($timeEntries as $entry) {
                $amount = $entry->hours * $entry->rate;
                $subtotal += $amount;
                $lineItems[] = [
                    'description' => $entry->description.' ('.$entry->hours.' hrs @ $'.$entry->rate.')',
                    'type' => 'time',
                    'reference_id' => $entry->id,
                    'quantity' => $entry->hours,
                    'unit_price' => $entry->rate,
                    'amount' => $amount,
                ];
                $entry->update(['status' => 'invoiced']);
            }
        }

        if (! empty($validated['expenses'])) {
            $expenses = Expense::whereIn('id', $validated['expenses'])->where('status', 'approved')->get();
            foreach ($expenses as $expense) {
                $subtotal += $expense->amount;
                $lineItems[] = [
                    'description' => $expense->description,
                    'type' => 'expense',
                    'reference_id' => $expense->id,
                    'quantity' => 1,
                    'unit_price' => $expense->amount,
                    'amount' => $expense->amount,
                ];
                $expense->update(['status' => 'invoiced']);
            }
        }

        $taxAmount = $validated['tax_amount'] ?? 0;
        $discountAmount = $validated['discount_amount'] ?? 0;
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'case_id' => $validated['case_id'] ?? null,
            'client_id' => $validated['client_id'],
            'created_by' => auth()->id(),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($lineItems as $item) {
            InvoiceLineItem::create(array_merge($item, ['invoice_id' => $invoice->id]));
        }

        return redirect()->route('billing.invoices.show', $invoice)->with('success', 'Invoice created successfully.');
    }

    public function showInvoice(Invoice $invoice): View
    {
        $invoice->load(['case', 'client', 'createdBy', 'lineItems']);

        return view('billing.show-invoice', compact('invoice'));
    }

    public function sendInvoice(Invoice $invoice): RedirectResponse
    {
        $invoice->update(['status' => 'sent']);

        return back()->with('success', 'Invoice marked as sent.');
    }

    public function markPaid(Invoice $invoice): RedirectResponse
    {
        $invoice->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);

        return back()->with('success', 'Invoice marked as paid.');
    }

    public function reports(): View
    {
        $totalRevenue = Invoice::where('status', 'paid')->sum('total_amount');
        $outstandingAmount = Invoice::whereIn('status', ['sent', 'viewed', 'overdue'])->sum('total_amount');
        $overdueAmount = Invoice::where('status', 'overdue')->sum('total_amount');

        $casesWithBilling = LegalCase::with(['assignedAttorney', 'invoices'])
            ->withSum('timeEntries as total_hours', 'hours')
            ->withSum('expenses as total_expenses', 'amount')
            ->get();

        return view('billing.reports', compact('totalRevenue', 'outstandingAmount', 'overdueAmount', 'casesWithBilling'));
    }
}
