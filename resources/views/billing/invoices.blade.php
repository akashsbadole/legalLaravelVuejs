@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Invoices</h2>
    <a href="{{ route('billing.invoices.create') }}" class="btn btn-primary">Create Invoice</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Client</th>
                    <th>Case</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->client->name ?? 'N/A' }}</td>
                        <td>{{ $invoice->case?->case_number ?? 'N/A' }}</td>
                        <td>${{ number_format($invoice->total_amount, 2) }}</td>
                        <td><span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'overdue' ? 'danger' : 'warning') }}">{{ $invoice->status }}</span></td>
                        <td>{{ $invoice->issue_date?->format('M d, Y') }}</td>
                        <td>{{ $invoice->due_date?->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('billing.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">View</a>
                            @if($invoice->status == 'draft')
                                <form method="POST" action="{{ route('billing.invoices.send', $invoice) }}" class="d-inline">@csrf <button class="btn btn-sm btn-success">Send</button></form>
                            @endif
                            @if($invoice->status == 'sent')
                                <form method="POST" action="{{ route('billing.invoices.paid', $invoice) }}" class="d-inline">@csrf <button class="btn btn-sm btn-primary">Mark Paid</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No invoices.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $invoices->links() }}
    </div>
</div>
@endsection