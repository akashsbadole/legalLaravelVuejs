@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Expenses</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">Add Expense</button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Case</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Billable</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->expense_date?->format('M d, Y') }}</td>
                        <td>{{ $expense->case?->case_number ?? 'N/A' }}</td>
                        <td>{{ $expense->category->name ?? 'N/A' }}</td>
                        <td>{{ Str::limit($expense->description, 40) }}</td>
                        <td>${{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $expense->is_billable ? 'Yes' : 'No' }}</td>
                        <td><span class="badge bg-{{ $expense->status == 'approved' ? 'success' : 'secondary' }}">{{ $expense->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No expenses.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $expenses->links() }}
    </div>
</div>

<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('billing.expenses.store') }}" class="modal-content">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Add Expense</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Case *</label>
                    <select name="case_id" class="form-select" required>
                        <option value="">Select Case</option>
                        @foreach($cases as $case)
                            <option value="{{ $case->id }}">{{ $case->case_number }} - {{ $case->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description *</label>
                    <input type="text" name="description" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Amount *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date *</label>
                        <input type="date" name="expense_date" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_billable" class="form-check-input" value="1" checked>
                        <label class="form-check-label">Billable</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button></div>
        </form>
    </div>
</div>
@endsection