<?php

namespace App\Http\Controllers;

use App\Models\LegalCase;
use App\Models\Invoice;
use App\Models\TimeEntry;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function cases(Request $request): View
    {
        $query = LegalCase::with(['status', 'assignedAttorney', 'client']);

        if ($request->filled('status')) {
            $query->where('case_status_id', $request->status);
        }

        if ($request->filled('attorney')) {
            $query->where('assigned_attorney_id', $request->attorney);
        }

        if ($request->filled('date_from')) {
            $query->where('filing_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('filing_date', '<=', $request->date_to);
        }

        if ($request->filled('case_type')) {
            $query->where('case_type', $request->case_type);
        }

        $cases = $query->get();
        $attorneys = User::whereIn('role', ['admin', 'attorney'])->get();
        $statuses = \App\Models\CaseStatus::all();

        return view('reports.cases', compact('cases', 'attorneys', 'statuses'));
    }

    public function financials(Request $request): View
    {
        $query = Invoice::with(['client', 'case']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        $invoices = $query->get();

        $totalRevenue = $invoices->where('status', 'paid')->sum('total_amount');
        $totalOutstanding = $invoices->whereIn('status', ['sent', 'overdue'])->sum('total_amount');
        $totalOverdue = $invoices->where('status', 'overdue')->sum('total_amount');

        $revenueByMonth = $invoices->where('status', 'paid')
            ->groupBy(function ($invoice) {
                return $invoice->paid_date->format('Y-m');
            })
            ->map(function ($group) {
                return $group->sum('total_amount');
            });

        return view('reports.financials', compact('invoices', 'totalRevenue', 'totalOutstanding', 'totalOverdue', 'revenueByMonth'));
    }

    public function timeTracking(Request $request): View
    {
        $query = TimeEntry::with(['user', 'case']);

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($request->filled('case')) {
            $query->where('case_id', $request->case);
        }

        if ($request->filled('date_from')) {
            $query->where('work_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('work_date', '<=', $request->date_to);
        }

        $entries = $query->get();

        $totalHours = $entries->sum('hours');
        $totalBillable = $entries->where('status', 'approved')->sum(fn ($e) => $e->hours * $e->rate);

        $hoursByUser = $entries->groupBy('user_id')->map(fn ($g) => $g->sum('hours'));
        $hoursByCase = $entries->groupBy('case_id')->map(fn ($g) => $g->sum('hours'));

        $users = User::whereIn('role', ['attorney', 'paralegal'])->get();
        $cases = LegalCase::all();

        return view('reports.time-tracking', compact('entries', 'totalHours', 'totalBillable', 'hoursByUser', 'hoursByCase', 'users', 'cases'));
    }

    public function attorneyPerformance(Request $request): View
    {
        $dateFrom = $request->filled('date_from') ? $request->date_from : null;
        $dateTo = $request->filled('date_to') ? $request->date_to : null;

        $attorneys = User::whereIn('role', ['admin', 'attorney'])->get()->map(function ($attorney) use ($dateFrom, $dateTo) {
            $cases = LegalCase::where('assigned_attorney_id', $attorney->id);
            
            if ($dateFrom) {
                $cases->where('filing_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $cases->where('filing_date', '<=', $dateTo);
            }

            $totalCases = $cases->count();
            $closedCases = $cases->clone()->whereHas('status', fn ($q) => $q->where('name', 'Closed'))->count();
            
            $timeEntries = TimeEntry::where('user_id', $attorney->id);
            $totalHours = $timeEntries->clone()->sum('hours');
            $billableAmount = $timeEntries->clone()->where('status', 'approved')->get()->sum(fn ($e) => $e->hours * $e->rate);

            return (object) [
                'attorney' => $attorney,
                'total_cases' => $totalCases,
                'closed_cases' => $closedCases,
                'total_hours' => $totalHours,
                'billable_amount' => $billableAmount,
            ];
        });

        return view('reports.attorney-performance', compact('attorneys'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'cases');
        
        switch ($type) {
            case 'cases':
                return $this->exportCases($request);
            case 'invoices':
                return $this->exportInvoices($request);
            case 'time-entries':
                return $this->exportTimeEntries($request);
            default:
                return back();
        }
    }

    private function exportCases(Request $request)
    {
        $cases = LegalCase::with(['status', 'assignedAttorney', 'client'])
            ->when($request->status, fn ($q) => $q->where('case_status_id', $request->status))
            ->when($request->attorney, fn ($q) => $q->where('assigned_attorney_id', $request->attorney))
            ->get();

        $headers = ['Case Number', 'Title', 'Status', 'Attorney', 'Client', 'Filing Date', 'Case Type'];
        $rows = $cases->map(fn ($c) => [
            $c->case_number,
            $c->title,
            $c->status?->name,
            $c->assignedAttorney?->name,
            $c->client?->name,
            $c->filing_date?->format('Y-m-d'),
            $c->case_type,
        ]);

        return $this->downloadCsv('cases-report.csv', $headers, $rows);
    }

    private function exportInvoices(Request $request)
    {
        $invoices = Invoice::with(['client', 'case'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->get();

        $headers = ['Invoice Number', 'Client', 'Case', 'Amount', 'Status', 'Issue Date', 'Due Date'];
        $rows = $invoices->map(fn ($i) => [
            $i->invoice_number,
            $i->client?->name,
            $i->case?->case_number,
            $i->total_amount,
            $i->status,
            $i->issue_date?->format('Y-m-d'),
            $i->due_date?->format('Y-m-d'),
        ]);

        return $this->downloadCsv('invoices-report.csv', $headers, $rows);
    }

    private function exportTimeEntries(Request $request)
    {
        $entries = TimeEntry::with(['user', 'case'])
            ->when($request->user, fn ($q) => $q->where('user_id', $request->user))
            ->get();

        $headers = ['Date', 'User', 'Case', 'Description', 'Hours', 'Rate', 'Status'];
        $rows = $entries->map(fn ($e) => [
            $e->work_date?->format('Y-m-d'),
            $e->user?->name,
            $e->case?->case_number,
            $e->description,
            $e->hours,
            $e->rate,
            $e->status,
        ]);

        return $this->downloadCsv('time-entries-report.csv', $headers, $rows);
    }

    private function downloadCsv(string $filename, array $headers, $rows)
    {
        $csv = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $csv .= implode(',', $row) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }
}
