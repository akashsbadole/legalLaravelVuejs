<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['case', 'client']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }

        $invoices = $query->paginate($request->get('per_page', 15));

        return response()->json($invoices);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'case_id' => 'nullable|exists:cases,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'line_items' => 'required|array',
        ]);

        $subtotal = 0;
        $lineItems = [];

        foreach ($validated['line_items'] as $item) {
            $amount = $item['quantity'] * $item['unit_price'];
            $subtotal += $amount;
            $lineItems[] = [
                'description' => $item['description'],
                'type' => $item['type'] ?? 'service',
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'amount' => $amount,
            ];
        }

        $taxAmount = $request->input('tax_amount', 0);
        $totalAmount = $subtotal + $taxAmount;

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'case_id' => $validated['case_id'] ?? null,
            'client_id' => $validated['client_id'],
            'created_by' => auth()->id(),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
        ]);

        foreach ($lineItems as $item) {
            $invoice->lineItems()->create($item);
        }

        return response()->json(['data' => $invoice, 'message' => 'Invoice created'], 201);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['case', 'client', 'createdBy', 'lineItems']);

        return response()->json(['data' => $invoice]);
    }

    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:draft,sent,paid,overdue',
            'due_date' => 'sometimes|date',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);

        return response()->json(['data' => $invoice, 'message' => 'Invoice updated']);
    }

    public function recordPayment(Request $request, Invoice $invoice): JsonResponse
    {
        $invoice->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);

        return response()->json(['data' => $invoice, 'message' => 'Payment recorded']);
    }
}
