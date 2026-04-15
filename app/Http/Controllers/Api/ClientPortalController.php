<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalCase;
use App\Models\Invoice;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientPortalController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $cases = LegalCase::where('client_id', $user->id)
            ->with(['status', 'assignedAttorney'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $invoices = Invoice::where('client_id', $user->id)
            ->whereIn('status', ['sent', 'overdue'])
            ->orderBy('due_date', 'asc')
            ->get();

        $documents = Document::where('client_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $activeCasesCount = $cases->whereIn('status_id', [1, 2])->count();
        $pendingAmount = $invoices->sum('total_amount');
        $documentsCount = Document::where('client_id', $user->id)->count();

        return response()->json([
            'stats' => [
                'activeCases' => $activeCasesCount,
                'pendingAmount' => $pendingAmount,
                'documents' => $documentsCount
            ],
            'recent_cases' => $cases->take(5)->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'case_number' => $c->case_number,
                'status' => $c->status?->name ?? 'Unknown',
                'description' => $c->description,
                'updated_at' => $c->updated_at->toDateTimeString()
            ]),
            'pending_invoices' => $invoices->map(fn($inv) => [
                'id' => $inv->id,
                'number' => $inv->invoice_number,
                'amount' => $inv->total_amount,
                'due_date' => $inv->due_date->toDateString(),
                'status' => $inv->status
            ]),
            'documents' => $documents->map(fn($doc) => [
                'id' => $doc->id,
                'name' => $doc->name,
                'created_at' => $doc->created_at->format('M d, Y')
            ])
        ]);
    }

    public function cases(Request $request): JsonResponse
    {
        $cases = LegalCase::where('client_id', $request->user()->id)
            ->with(['status', 'assignedAttorney', 'tasks', 'documents'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'cases' => $cases->items(),
            'total' => $cases->total(),
            'current_page' => $cases->currentPage(),
            'last_page' => $cases->lastPage()
        ]);
    }

    public function invoices(Request $request): JsonResponse
    {
        $invoices = Invoice::where('client_id', $request->user()->id)
            ->with(['lineItems'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'invoices' => $invoices->items(),
            'total' => $invoices->total(),
            'current_page' => $invoices->currentPage(),
            'last_page' => $invoices->lastPage()
        ]);
    }

    public function documents(Request $request): JsonResponse
    {
        $documents = Document::where('client_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'documents' => $documents->items(),
            'total' => $documents->total(),
            'current_page' => $documents->currentPage(),
            'last_page' => $documents->lastPage()
        ]);
    }
}