@extends('layouts.app')

@section('title', 'Case Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>{{ $case->case_number }}</h2>
        <h4>{{ $case->title }}</h4>
    </div>
    <div>
        <a href="{{ route('cases.edit', $case) }}" class="btn btn-secondary">Edit</a>
        <form method="POST" action="{{ route('cases.destroy', $case) }}" class="d-inline">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this case?')">Delete</button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">Case Information</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span class="badge" style="background-color: {{ $case->status->color ?? '#6c757d' }}">{{ $case->status->name ?? 'N/A' }}</span></p>
                        <p><strong>Type:</strong> {{ $case->case_type ?? 'N/A' }}</p>
                        <p><strong>Assigned Attorney:</strong> {{ $case->assignedAttorney->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Open Date:</strong> {{ $case->open_date?->format('M d, Y') }}</p>
                        <p><strong>Expected Close:</strong> {{ $case->expected_close_date?->format('M d, Y') }}</p>
                        <p><strong>Estimated Value:</strong> {{ $case->estimated_value ? '$' . number_format($case->estimated_value, 2) : 'N/A' }}</p>
                    </div>
                </div>
                <p><strong>Description:</strong> {{ $case->description ?? 'No description' }}</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span>Notes</span>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">Add Note</button>
            </div>
            <div class="card-body">
                @forelse($case->notes->sortByDesc('created_at') as $note)
                    <div class="border-bottom pb-2 mb-2">
                        <small class="text-muted">{{ $note->user->name ?? 'Unknown' }} - {{ $note->created_at->format('M d, Y H:i') }}</small>
                        <p class="mb-1">{{ $note->content }}</p>
                    </div>
                @empty
                    <p class="text-muted">No notes yet.</p>
                @endforelse
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span>Documents</span>
                <a href="{{ route('documents.generate') }}?case={{ $case->id }}" class="btn btn-sm btn-primary">Generate</a>
            </div>
            <div class="card-body">
                @forelse($case->documents as $doc)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <span>{{ $doc->name }}</span>
                        <a href="{{ route('documents.download', $doc) }}" class="btn btn-sm btn-outline-secondary">Download</a>
                    </div>
                @empty
                    <p class="text-muted">No documents.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">Participants</div>
            <div class="card-body">
                @forelse($case->participants as $participant)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>{{ $participant->user->name ?? 'Unknown' }}</span>
                        <small class="text-muted">{{ $participant->role }}</small>
                    </div>
                @empty
                    <p class="text-muted">No participants.</p>
                @endforelse
                <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#addParticipantModal">Add</button>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Financial Summary</div>
            <div class="card-body">
                <p><strong>Total Hours:</strong> {{ number_format($case->total_hours, 2) }}</p>
                <p><strong>Total Expenses:</strong> ${{ number_format($case->total_expenses, 2) }}</p>
                <p><strong>Total Billed:</strong> ${{ number_format($case->total_billed, 2) }}</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('cases.notes.store', $case) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_internal" class="form-check-input" value="0" id="internalCheck">
                        <label class="form-check-label" for="internalCheck">Internal only</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addParticipantModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('cases.participants.store', $case) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Participant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">Select User</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="client">Client</option>
                        <option value="opposing_counsel">Opposing Counsel</option>
                        <option value="judge">Judge</option>
                        <option value="witness">Witness</option>
                        <option value="expert">Expert</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection