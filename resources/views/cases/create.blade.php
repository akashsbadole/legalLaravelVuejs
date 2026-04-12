@extends('layouts.app')

@section('title', 'Create Case')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>New Case</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('cases.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Case Type</label>
                    <input type="text" name="case_type" class="form-control" placeholder="e.g., Civil, Criminal, Family">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status *</label>
                    <select name="status_id" class="form-select" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Assigned Attorney *</label>
                    <select name="assigned_attorney_id" class="form-select" required>
                        <option value="">Select Attorney</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Open Date *</label>
                    <input type="date" name="open_date" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Expected Close Date</label>
                    <input type="date" name="expected_close_date" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Estimated Value</label>
                    <input type="number" name="estimated_value" class="form-control" step="0.01">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Case</button>
            <a href="{{ route('cases.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection