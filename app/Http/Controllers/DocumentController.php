<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\DocumentCategory;
use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Document::with(['case', 'createdBy', 'template']);

        if ($request->filled('case')) {
            $query->where('case_id', $request->case);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('documents.index', compact('documents'));
    }

    public function templates(): View
    {
        $templates = DocumentTemplate::with(['category', 'createdBy'])->orderBy('name')->get();
        $categories = DocumentCategory::all();

        return view('documents.templates', compact('templates', 'categories'));
    }

    public function createTemplate(): View
    {
        $categories = DocumentCategory::all();
        return view('documents.create-template', compact('categories'));
    }

    public function storeTemplate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:document_categories,id',
            'content' => 'required|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['version'] = 1;
        $validated['is_active'] = true;

        DocumentTemplate::create($validated);

        return redirect()->route('documents.templates')->with('success', 'Template created successfully.');
    }

    public function editTemplate(DocumentTemplate $template): View
    {
        $categories = DocumentCategory::all();
        return view('documents.edit-template', compact('template', 'categories'));
    }

    public function updateTemplate(Request $request, DocumentTemplate $template): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:document_categories,id',
            'content' => 'required|string',
        ]);

        $template->update($validated);

        return redirect()->route('documents.templates')->with('success', 'Template updated successfully.');
    }

    public function previewTemplate(Request $request, DocumentTemplate $template): View
    {
        $placeholders = $template->getPlaceholders();
        $sampleData = [];

        foreach ($placeholders as $placeholder) {
            $sampleData[$placeholder] = match($placeholder) {
                'client_name' => 'John Doe',
                'case_number' => 'CASE-2024-0001',
                'date' => now()->format('F d, Y'),
                'attorney_name' => 'Jane Smith',
                default => ucwords(str_replace('_', ' ', $placeholder))
            };
        }

        $content = $template->content;
        foreach ($sampleData as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }

        return view('documents.preview', compact('template', 'content', 'sampleData'));
    }

    public function generate(Request $request): View
    {
        $templates = DocumentTemplate::where('is_active', true)->get();
        $cases = LegalCase::with('assignedAttorney')->get();

        return view('documents.generate', compact('templates', 'cases'));
    }

    public function generateDocument(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:document_templates,id',
            'case_id' => 'nullable|exists:cases,id',
        ]);

        $template = DocumentTemplate::findOrFail($validated['template_id']);
        $case = isset($validated['case_id']) ? LegalCase::with(['assignedAttorney', 'participants.user'])->findOrFail($validated['case_id']) : null;

        $data = [];
        
        if ($case) {
            $data = [
                'case_number' => $case->case_number,
                'case_title' => $case->title,
                'client_name' => $case->participants->where('role', 'client')->first()?->user?->name ?? 'N/A',
                'attorney_name' => $case->assignedAttorney->name ?? 'N/A',
                'open_date' => $case->open_date?->format('F d, Y'),
                'expected_close_date' => $case->expected_close_date?->format('F d, Y'),
                'status' => $case->status->name ?? 'N/A',
            ];
        }

        $content = $template->content;
        foreach ($data as $key => $value) {
            $content = str_replace("{{{$key}}}", $value ?? '', $content);
        }

        preg_match_all('/{{\s*(\w+)\s*}}/', $content, $unfilled);
        if (!empty($unfilled[1])) {
            return back()->with('error', 'Please fill all placeholders: ' . implode(', ', array_unique($unfilled[1])));
        }

        $caseIdPart = isset($validated['case_id']) ? $validated['case_id'] : 'general';
        $document = Document::create([
            'name' => $template->name . '_' . $caseIdPart . '_' . now()->format('YmdHis'),
            'case_id' => $validated['case_id'] ?? null,
            'template_id' => $template->id,
            'created_by' => auth()->id(),
            'file_path' => 'documents/' . now()->format('Y/m/d') . '/' . uniqid() . '.html',
            'file_type' => 'html',
            'content' => $content,
            'version' => 1,
            'is_generated' => true,
        ]);

        Storage::disk('public')->put($document->file_path, $content);

        return redirect()->route('documents.show', $document)->with('success', 'Document generated successfully.');
    }

    public function show(Document $document): View
    {
        $document->load(['case', 'createdBy', 'template']);
        return view('documents.show', compact('document'));
    }

    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($document->file_path, $document->name . '.' . $document->file_type);
    }

    public function destroy(Document $document): RedirectResponse
    {
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document deleted successfully.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'case_id' => 'nullable|exists:cases,id',
            'file' => 'required|file|mimes:pdf,doc,docx,txt,html',
        ]);

        $path = $request->file('file')->store('documents/' . now()->format('Y/m/d'), 'public');

        Document::create([
            'name' => $validated['name'],
            'case_id' => $validated['case_id'] ?? null,
            'created_by' => auth()->id(),
            'file_path' => $path,
            'file_type' => $request->file('file')->getClientOriginalExtension(),
            'is_generated' => false,
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }
}