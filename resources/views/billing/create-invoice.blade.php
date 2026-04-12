@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div class="card">
    <div class="card-header"><h4>Create Invoice</h4></div>
    <div class="card-body">
        <form method="POST" action="{{ route('billing.invoices.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Client *</label>
                    <select name="client_id" class="form-select" required>
                        <option value="">Select Client</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Case (Optional)</label>
                    <select name="case_id" class="form-select">
                        <option value="">No Case</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Issue Date *</label>
                    <input type="date" name="issue_date" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Due Date *</label>
                    <input type="date" name="due_date" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tax Amount</label>
                    <input type="number" name="tax_amount" class="form-control" step="0.01" value="0">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Invoice</button>
            <a href="{{ route('billing.invoices') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection