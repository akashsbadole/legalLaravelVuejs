@extends('layouts.app')

@section('title', 'Edit Case')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Edit Case</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('cases.update', $case) }}">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ $case->title }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Case Type</label>
                    <input type="text" name="case_type" class="form-control" value="{{ $case->case_type }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $case->description }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status *</label>
                    <select name="status_id" class="form-select" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ $case->status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Assigned Attorney *</label>
                    <select name="assigned_attorney_id" class="form-select" required>
                        <option value="{{ $case->assigned_attorney_id }}">{{ $case->assignedAttorney->name }}</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Open Date *</label>
                    <input type="date" name="open_date" class="form-control" value="{{ $case->open_date?->format('Y-m-d') }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Expected Close Date</label>
                    <input type="date" name="expected_close_date" class="form-control" value="{{ $case->expected_close_date?->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Actual Close Date</label>
                    <input type="date" name="actual_close_date" class="form-control" value="{{ $case->actual_close_date?->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Estimated Value</label>
                    <input type="number" name="estimated_value" class="form-control" step="0.01" value="{{ $case->estimated_value }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ $case->notes }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Case</button>
            <a href="{{ route('cases.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection