@extends('layouts.app')

@section('title', 'Cases')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Cases</h2>
    <a href="{{ route('cases.create') }}" class="btn btn-primary">New Case</a>
</div>

<form method="GET" class="row g-3 mb-4">
    <div class="col-md-3">
        <input type="text" name="search" class="form-control" placeholder="Search cases..." value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">All Statuses</option>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-1">
        <button type="submit" class="btn btn-secondary">Filter</button>
    </div>
</form>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Case Number</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Attorney</th>
                    <th>Open Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cases as $case)
                    <tr>
                        <td>{{ $case->case_number }}</td>
                        <td>{{ $case->title }}</td>
                        <td><span class="badge" style="background-color: {{ $case->status->color ?? '#6c757d' }}">{{ $case->status->name ?? 'N/A' }}</span></td>
                        <td>{{ $case->assignedAttorney->name ?? 'N/A' }}</td>
                        <td>{{ $case->open_date?->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('cases.show', $case) }}" class="btn btn-sm btn-outline-primary">View</a>
                            <a href="{{ route('cases.edit', $case) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No cases found.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $cases->links() }}
    </div>
</div>
@endsection