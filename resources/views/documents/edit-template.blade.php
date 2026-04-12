@extends('layouts.app')

@section('title', 'Edit Template')

@section('content')
<div class="card">
    <div class="card-header"><h4>Edit Template</h4></div>
    <div class="card-body">
        <form method="POST" action="{{ route('documents.templates.update', $template) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Name *</label>
                <input type="text" name="name" class="form-control" value="{{ $template->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2">{{ $template->description }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $template->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Content *</label>
                <textarea name="content" class="form-control" rows="10">{{ $template->content }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('documents.templates') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection