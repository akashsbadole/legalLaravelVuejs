<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\LegalCase;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function exportCases(Request $request)
    {
        $cases = LegalCase::with(['status', 'assignedAttorney'])->get();

        $csvData = [];
        $csvData[] = ['Case Number', 'Title', 'Type', 'Status', 'Attorney', 'Open Date', 'Expected Close', 'Estimated Value'];

        foreach ($cases as $case) {
            $csvData[] = [
                $case->case_number,
                $case->title,
                $case->case_type ?? 'N/A',
                $case->status->name ?? 'N/A',
                $case->assignedAttorney->name ?? 'N/A',
                $case->open_date?->format('Y-m-d'),
                $case->expected_close_date?->format('Y-m-d'),
                $case->estimated_value ?? 0,
            ];
        }

        return $this->downloadCsv($csvData, 'cases_export_'.date('Y-m-d').'.csv');
    }

    public function exportInvoices(Request $request)
    {
        $invoices = Invoice::with(['client', 'case'])->get();

        $csvData = [];
        $csvData[] = ['Invoice Number', 'Client', 'Case', 'Amount', 'Status', 'Issue Date', 'Due Date', 'Paid Date'];

        foreach ($invoices as $invoice) {
            $csvData[] = [
                $invoice->invoice_number,
                $invoice->client->name ?? 'N/A',
                $invoice->case->case_number ?? 'N/A',
                $invoice->total_amount,
                $invoice->status,
                $invoice->issue_date?->format('Y-m-d'),
                $invoice->due_date?->format('Y-m-d'),
                $invoice->paid_date?->format('Y-m-d') ?? 'N/A',
            ];
        }

        return $this->downloadCsv($csvData, 'invoices_export_'.date('Y-m-d').'.csv');
    }

    public function exportTimeEntries(Request $request)
    {
        $query = TimeEntry::with(['case', 'user']);

        if ($request->filled('case_id')) {
            $query->where('case_id', $request->case_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $entries = $query->get();

        $csvData = [];
        $csvData[] = ['Date', 'Case', 'User', 'Description', 'Hours', 'Rate', 'Amount', 'Status'];

        foreach ($entries as $entry) {
            $csvData[] = [
                $entry->work_date?->format('Y-m-d'),
                $entry->case->case_number ?? 'N/A',
                $entry->user->name ?? 'N/A',
                $entry->description,
                $entry->hours,
                $entry->rate,
                $entry->hours * $entry->rate,
                $entry->status,
            ];
        }

        return $this->downloadCsv($csvData, 'time_entries_export_'.date('Y-m-d').'.csv');
    }

    public function exportExpenses(Request $request)
    {
        $query = Expense::with(['case', 'user', 'category']);

        if ($request->filled('case_id')) {
            $query->where('case_id', $request->case_id);
        }

        $expenses = $query->get();

        $csvData = [];
        $csvData[] = ['Date', 'Case', 'Category', 'User', 'Description', 'Amount', 'Billable', 'Status'];

        foreach ($expenses as $expense) {
            $csvData[] = [
                $expense->expense_date?->format('Y-m-d'),
                $expense->case->case_number ?? 'N/A',
                $expense->category->name ?? 'N/A',
                $expense->user->name ?? 'N/A',
                $expense->description,
                $expense->amount,
                $expense->is_billable ? 'Yes' : 'No',
                $expense->status,
            ];
        }

        return $this->downloadCsv($csvData, 'expenses_export_'.date('Y-m-d').'.csv');
    }

    private function downloadCsv(array $data, string $filename): StreamedResponse
    {
        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
