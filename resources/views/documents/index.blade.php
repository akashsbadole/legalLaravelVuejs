@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Documents</h2>
    <div>
        <a href="{{ route('documents.templates') }}" class="btn btn-outline-secondary">Templates</a>
        <a href="{{ route('documents.generate') }}" class="btn btn-primary">Generate Document</a>
    </div>
</div>

<form method="GET" class="row g-3 mb-4">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search documents..." value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-secondary">Search</button>
    </div>
</form>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Case</th>
                    <th>Type</th>
                    <th>Created By</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td>{{ $doc->name }}</td>
                        <td>{{ $doc->case?->case_number ?? 'N/A' }}</td>
                        <td>{{ $doc->is_generated ? 'Generated' : 'Uploaded' }}</td>
                        <td>{{ $doc->createdBy->name ?? 'N/A' }}</td>
                        <td>{{ $doc->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('documents.show', $doc) }}" class="btn btn-sm btn-outline-primary">View</a>
                            <a href="{{ route('documents.download', $doc) }}" class="btn btn-sm btn-outline-secondary">Download</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No documents found.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $documents->links() }}
    </div>
</div>
@endsection