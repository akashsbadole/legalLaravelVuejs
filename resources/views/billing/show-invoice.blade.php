@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between">
        <h4>Invoice #{{ $invoice->invoice_number }}</h4>
        <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'overdue' ? 'danger' : 'warning') }} fs-6">{{ $invoice->status }}</span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Client:</strong> {{ $invoice->client->name ?? 'N/A' }}</p>
                <p><strong>Case:</strong> {{ $invoice->case?->case_number ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Issue Date:</strong> {{ $invoice->issue_date?->format('M d, Y') }}</p>
                <p><strong>Due Date:</strong> {{ $invoice->due_date?->format('M d, Y') }}</p>
                <p><strong>Paid Date:</strong> {{ $invoice->paid_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><h5>Line Items</h5></div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->lineItems as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->type }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->unit_price, 2) }}</td>
                        <td>${{ number_format($item->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No line items.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                    <td>${{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->tax_amount > 0)
                <tr>
                    <td colspan="4" class="text-end"><strong>Tax:</strong></td>
                    <td>${{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
                @endif
                @if($invoice->discount_amount > 0)
                <tr>
                    <td colspan="4" class="text-end"><strong>Discount:</strong></td>
                    <td>-${{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                    <td><strong>${{ number_format($invoice->total_amount, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="d-flex gap-2">
    @if($invoice->status == 'draft')
        <form method="POST" action="{{ route('billing.invoices.send', $invoice) }}">@csrf <button class="btn btn-success">Mark as Sent</button></form>
    @endif
    @if($invoice->status == 'sent')
        <form method="POST" action="{{ route('billing.invoices.paid', $invoice) }}">@csrf <button class="btn btn-primary">Mark as Paid</button></form>
    @endif
    <a href="{{ route('billing.invoices') }}" class="btn btn-secondary">Back</a>
</div>
@endsection