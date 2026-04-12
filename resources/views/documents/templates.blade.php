@extends('layouts.app')

@section('title', 'Document Templates')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Document Templates</h2>
    <a href="{{ route('documents.templates.create') }}" class="btn btn-primary">New Template</a>
</div>

<div class="row">
    @forelse($templates as $template)
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5>{{ $template->name }}</h5>
                    <p class="text-muted">{{ $template->description }}</p>
                    <small class="text-muted">Category: {{ $template->category->name ?? 'None' }}</small><br>
                    <small class="text-muted">Version: {{ $template->version }}</small>
                    <div class="mt-3">
                        <a href="{{ route('documents.templates.preview', $template) }}" class="btn btn-sm btn-outline-primary">Preview</a>
                        <a href="{{ route('documents.templates.edit', $template) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center">
            <p class="text-muted">No templates yet.</p>
        </div>
    @endforelse
</div>
@endsection