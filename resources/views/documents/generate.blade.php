@extends('layouts.app')

@section('title', 'Generate Document')

@section('content')
<div class="card">
    <div class="card-header"><h4>Generate Document</h4></div>
    <div class="card-body">
        <form method="POST" action="{{ route('documents.generate.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Template *</label>
                <select name="template_id" class="form-select" required>
                    <option value="">Select Template</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Case (Optional)</label>
                <select name="case_id" class="form-select">
                    <option value="">No Case</option>
                    @foreach($cases as $case)
                        <option value="{{ $case->id }}">{{ $case->case_number }} - {{ $case->title }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Generate</button>
        </form>
    </div>
</div>
@endsection