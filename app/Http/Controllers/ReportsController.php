<?php

namespace App\Http\Controllers;

use App\Models\CaseStatus;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\LegalCase;
use App\Models\TrustAccount;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            // Financial Overview
            $totalRevenue = Invoice::where('status', 'paid')->sum('total_amount');
            $outstandingAmount = Invoice::where('status', 'sent')->sum('total_amount');
            $overdueAmount = Invoice::where('status', 'overdue')->sum('total_amount');

            // Case Statistics
            $casesWithBilling = LegalCase::with(['assignedAttorney', 'timeEntries', 'expenses', 'invoices'])
                ->where(function ($query) {
                    $query->whereHas('timeEntries')
                        ->orWhereHas('expenses')
                        ->orWhereHas('invoices');
                })
                ->get();

            // Monthly Revenue Trend (Last 12 months)
            $monthlyRevenue = Invoice::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
                ->where('status', 'paid')
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            // Attorney Performance
            $attorneyStats = User::where('role', 'attorney')
                ->with(['assignedCases.timeEntries', 'assignedCases.invoices'])
                ->get()
                ->map(function ($attorney) {
                    $cases = $attorney->assignedCases;
                    $totalHours = $cases->sum(function ($case) {
                        return $case->timeEntries->sum('hours');
                    });
                    $totalRevenue = $cases->sum(function ($case) {
                        return $case->invoices->where('status', 'paid')->sum('total_amount');
                    });

                    return [
                        'attorney' => $attorney,
                        'total_cases' => $cases->count(),
                        'total_hours' => $totalHours,
                        'total_revenue' => $totalRevenue,
                        'avg_hourly_rate' => $totalHours > 0 ? $totalRevenue / $totalHours : 0,
                    ];
                });

            // Client Statistics
            $clientStats = User::where('role', 'client')
                ->with(['cases.invoices', 'cases.timeEntries'])
                ->get()
                ->map(function ($client) {
                    $totalSpent = $client->cases->sum(function ($case) {
                        return $case->invoices->where('status', 'paid')->sum('total_amount');
                    });
                    $totalHours = $client->cases->sum(function ($case) {
                        return $case->timeEntries->sum('hours');
                    });

                    return [
                        'client' => $client,
                        'total_cases' => $client->cases->count(),
                        'total_spent' => $totalSpent,
                        'total_hours' => $totalHours,
                    ];
                })->sortByDesc('total_spent')->take(10);

            return response()->json(compact(
                'totalRevenue',
                'outstandingAmount',
                'overdueAmount',
                'casesWithBilling',
                'monthlyRevenue',
                'attorneyStats',
                'clientStats'
            ));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate reports',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function financial(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Revenue by period
        $revenueByPeriod = Invoice::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as revenue')
        )
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Expenses by category
        $expensesByCategory = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('category')
            ->get();

        // Trust account balances
        $trustBalances = TrustAccount::select('client_id',
            DB::raw('MAX(balance) as current_balance'),
            DB::raw('SUM(CASE WHEN transaction_type = "deposit" THEN amount ELSE 0 END) as total_deposits'),
            DB::raw('SUM(CASE WHEN transaction_type IN ("withdrawal", "transfer") THEN amount ELSE 0 END) as total_withdrawals')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('client_id')
            ->with('client')
            ->get();

        return response()->json(compact(
            'revenueByPeriod',
            'expensesByCategory',
            'trustBalances',
            'startDate',
            'endDate'
        ));
    }

    public function cases(Request $request): JsonResponse
    {
        $status = $request->get('status');
        $attorney = $request->get('attorney');

        $query = LegalCase::with(['assignedAttorney', 'client', 'timeEntries', 'invoices']);

        if ($status) {
            $query->where('status_id', $status);
        }

        if ($attorney) {
            $query->where('assigned_attorney_id', $attorney);
        }

        $cases = $query->get()->map(function ($case) {
            return [
                'case' => $case,
                'total_hours' => $case->timeEntries->sum('hours'),
                'total_billed' => $case->invoices->sum('total_amount'),
                'total_paid' => $case->invoices->where('status', 'paid')->sum('total_amount'),
                'outstanding' => $case->invoices->whereIn('status', ['sent', 'overdue'])->sum('total_amount'),
            ];
        });

        $attorneys = User::where('role', 'attorney')->get();
        $statuses = CaseStatus::all();

        return response()->json(compact('cases', 'attorneys', 'statuses', 'status', 'attorney'));
    }

    public function export(Request $request): StreamedResponse
    {
        $type = $request->get('type', 'financial');
        $format = $request->get('format', 'csv');

        $filename = "report-{$type}-".now()->format('Y-m-d').".{$format}";

        return response()->stream(function () use ($type, $format, $request) {
            $handle = fopen('php://output', 'w');

            switch ($type) {
                case 'financial':
                    $this->exportFinancialReport($handle, $format, $request);
                    break;
                case 'cases':
                    $this->exportCasesReport($handle, $format, $request);
                    break;
                case 'attorneys':
                    $this->exportAttorneysReport($handle, $format, $request);
                    break;
                case 'clients':
                    $this->exportClientsReport($handle, $format, $request);
                    break;
                default:
                    $this->exportFinancialReport($handle, $format, $request);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportFinancialReport($handle, $format, $request)
    {
        // CSV Headers
        fputcsv($handle, ['Date', 'Revenue', 'Expenses', 'Net Income']);

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $data = DB::table('invoices')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        foreach ($data as $row) {
            fputcsv($handle, [
                $row->date,
                number_format($row->revenue, 2),
                '0.00', // Expenses would need to be calculated
                number_format($row->revenue, 2),
            ]);
        }
    }

    private function exportCasesReport($handle, $format, $request)
    {
        fputcsv($handle, ['Case Number', 'Title', 'Client', 'Attorney', 'Status', 'Total Hours', 'Total Billed', 'Total Paid', 'Outstanding']);

        $query = LegalCase::with(['assignedAttorney', 'client', 'status', 'timeEntries', 'invoices']);

        if ($request->status) {
            $query->where('status_id', $request->status);
        }

        if ($request->attorney) {
            $query->where('assigned_attorney_id', $request->attorney);
        }

        $cases = $query->get();

        foreach ($cases as $case) {
            fputcsv($handle, [
                $case->case_number,
                $case->title,
                $case->client?->name ?? 'N/A',
                $case->assignedAttorney?->name ?? 'N/A',
                $case->status?->name ?? 'N/A',
                $case->timeEntries->sum('hours'),
                number_format($case->invoices->sum('total_amount'), 2),
                number_format($case->invoices->where('status', 'paid')->sum('total_amount'), 2),
                number_format($case->invoices->whereIn('status', ['sent', 'overdue'])->sum('total_amount'), 2),
            ]);
        }
    }

    private function exportAttorneysReport($handle, $format, $request)
    {
        fputcsv($handle, ['Attorney', 'Total Cases', 'Total Hours', 'Total Revenue', 'Average Hourly Rate']);

        $attorneys = User::where('role', 'attorney')
            ->with(['assignedCases.timeEntries', 'assignedCases.invoices'])
            ->get();

        foreach ($attorneys as $attorney) {
            $cases = $attorney->assignedCases;
            $totalHours = $cases->sum(function ($case) {
                return $case->timeEntries->sum('hours');
            });
            $totalRevenue = $cases->sum(function ($case) {
                return $case->invoices->where('status', 'paid')->sum('total_amount');
            });
            $avgRate = $totalHours > 0 ? $totalRevenue / $totalHours : 0;

            fputcsv($handle, [
                $attorney->name,
                $cases->count(),
                number_format($totalHours, 1),
                number_format($totalRevenue, 2),
                number_format($avgRate, 2),
            ]);
        }
    }

    private function exportClientsReport($handle, $format, $request)
    {
        fputcsv($handle, ['Client', 'Total Cases', 'Total Hours', 'Total Spent', 'Average per Case']);

        $clients = User::where('role', 'client')
            ->with(['cases.invoices', 'cases.timeEntries'])
            ->get();

        foreach ($clients as $client) {
            $totalSpent = $client->cases->sum(function ($case) {
                return $case->invoices->where('status', 'paid')->sum('total_amount');
            });
            $totalHours = $client->cases->sum(function ($case) {
                return $case->timeEntries->sum('hours');
            });
            $totalCases = $client->cases->count();
            $avgPerCase = $totalCases > 0 ? $totalSpent / $totalCases : 0;

            fputcsv($handle, [
                $client->name,
                $totalCases,
                number_format($totalHours, 1),
                number_format($totalSpent, 2),
                number_format($avgPerCase, 2),
            ]);
        }
    }
}
