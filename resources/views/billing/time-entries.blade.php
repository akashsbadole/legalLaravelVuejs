@extends('layouts.app')

@section('title', 'Time Entries')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Time Entries</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTimeEntryModal">Add Entry</button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Case</th>
                    <th>Description</th>
                    <th>Hours</th>
                    <th>Rate</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr>
                        <td>{{ $entry->work_date?->format('M d, Y') }}</td>
                        <td>{{ $entry->case?->case_number ?? 'N/A' }}</td>
                        <td>{{ Str::limit($entry->description, 50) }}</td>
                        <td>{{ $entry->hours }}</td>
                        <td>${{ number_format($entry->rate, 2) }}</td>
                        <td>${{ number_format($entry->hours * $entry->rate, 2) }}</td>
                        <td><span class="badge bg-{{ $entry->status == 'approved' ? 'success' : ($entry->status == 'rejected' ? 'danger' : 'secondary') }}">{{ $entry->status }}</span></td>
                        <td>
                            @if($entry->status == 'draft')
                                <form method="POST" action="{{ route('billing.time-entries.approve', $entry) }}" class="d-inline">@csrf <button class="btn btn-sm btn-success">Approve</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No time entries.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $entries->links() }}
    </div>
</div>

<div class="modal fade" id="addTimeEntryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('billing.time-entries.store') }}" class="modal-content">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Add Time Entry</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
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
                    <label class="form-label">Description *</label>
                    <textarea name="description" class="form-control" rows="2" required></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hours *</label>
                        <input type="number" name="hours" class="form-control" step="0.1" min="0.1" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Work Date *</label>
                        <input type="date" name="work_date" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button></div>
        </form>
    </div>
</div>
@endsection