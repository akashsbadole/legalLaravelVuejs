@extends('layouts.app')

@section('title', 'Create Template')

@section('content')
<div class="card">
    <div class="card-header"><h4>Create Template</h4></div>
    <div class="card-body">
        <form method="POST" action="{{ route('documents.templates.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Name *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Content *</label>
                <textarea name="content" class="form-control" rows="10" placeholder="Use {{ placeholder }} for dynamic fields"></textarea>
                <small class="text-muted">Example: {{ client_name }}, {{ case_number }}</small>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('documents.templates') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection