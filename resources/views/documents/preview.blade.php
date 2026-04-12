@extends('layouts.app')

@section('title', 'Preview Template')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h4>Preview: {{ $template->name }}</h4>
    </div>
    <div class="card-body">
        <div class="bg-white p-4 border" style="white-space: pre-wrap;">{!! $content !!}</div>
    </div>
</div>
<div class="mb-3">
    <strong>Available Placeholders:</strong>
    <ul>
        @foreach($template->getPlaceholders() as $placeholder)
            <li>{{ $placeholder }}</li>
        @endforeach
    </ul>
</div>
<a href="{{ route('documents.templates') }}" class="btn btn-secondary">Back</a>
@endsection