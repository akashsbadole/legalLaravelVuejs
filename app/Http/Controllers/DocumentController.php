<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentTemplate;
use App\Models\LegalCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Document::with(['case', 'createdBy', 'template']);

        // Filter documents based on user role
        $user = auth()->user();
        if ($user->isClient()) {
            // Clients can only see documents from cases they are participants in
            $query->whereHas('case.participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif (! $user->isAdmin() && ! $user->isAttorney()) {
            // Paralegals and others can see documents from cases they are assigned to or participants in
            $query->whereHas('case', function ($q) use ($user) {
                $q->where('assigned_attorney_id', $user->id)
                    ->orWhereHas('participants', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
            });
        }
        // Admins and attorneys can see all documents

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

        $validated['created_by'] = Auth::id();
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
            $sampleData[$placeholder] = match ($placeholder) {
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
        if (! empty($unfilled[1])) {
            return back()->with('error', 'Please fill all placeholders: '.implode(', ', array_unique($unfilled[1])));
        }

        $caseIdPart = isset($validated['case_id']) ? $validated['case_id'] : 'general';
        $document = Document::create([
            'name' => $template->name.'_'.$caseIdPart.'_'.now()->format('YmdHis'),
            'case_id' => $validated['case_id'] ?? null,
            'template_id' => $template->id,
            'created_by' => Auth::id(),
            'file_path' => 'documents/'.now()->format('Y/m/d').'/'.uniqid().'.html',
            'file_type' => 'html',
            'content' => $content,
            'version' => 1,
            'is_generated' => true,
        ]);

        Storage::disk('public')->put($document->file_path, $content);

        // Notify clients if document is associated with a case
        if ($validated['case_id']) {
            $case = LegalCase::find($validated['case_id']);
            if ($case) {
                // Notify all client participants in the case
                $clientParticipants = $case->participants()->wherePivot('role', 'client')->get();
                foreach ($clientParticipants as $participant) {
                    AppNotification::notifyNewDocument(
                        $participant->user_id,
                        $document->name,
                        $case->case_number
                    );
                }
            }
        }

        return redirect()->route('documents.show', $document)->with('success', 'Document generated successfully.');
    }

    public function show(Document $document): View
    {
        $this->authorize('view', $document);
        $document->load(['case', 'createdBy', 'template']);

        return view('documents.show', compact('document'));
    }

    public function download(Document $document)
    {
        $this->authorize('view', $document);
        if (! Storage::disk('s3')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $document->file_path,
            now()->addMinutes(5),
            [
                'ResponseContentDisposition' => 'attachment; filename="'.$document->name.'.'.$document->file_type.'"',
            ]
        );

        return redirect($url);
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);
        if (Storage::disk('s3')->exists($document->file_path)) {
            Storage::disk('s3')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document deleted successfully.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'case_id' => 'nullable|exists:cases,id',
            'file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,txt,html',
                'max:51200', // 50MB max
                function ($attribute, $value, $fail) {
                    // Additional security checks
                    if ($value) {
                        $fileName = $value->getClientOriginalName();

                        // Check for suspicious file names
                        if (preg_match('/[<>\"\|\?\*\:]/', $fileName)) {
                            $fail('Invalid file name. File names cannot contain special characters.');

                            return;
                        }

                        // Check file content for potential security issues
                        $content = file_get_contents($value->getRealPath());

                        // Check for embedded scripts in text files
                        if (in_array($value->getClientOriginalExtension(), ['txt', 'html'])) {
                            if (preg_match('/<script|javascript:|vbscript:|onload=|onerror=/i', $content)) {
                                $fail('File contains potentially malicious content.');

                                return;
                            }
                        }

                        // Check for PHP code in any file
                        if (preg_match('/<\?php|\beval\s*\(|\bexec\s*\(|\bsystem\s*\(/i', $content)) {
                            $fail('File contains potentially malicious content.');

                            return;
                        }
                    }
                },
            ],
        ]);

        $path = $request->file('file')->store('documents/'.now()->format('Y/m/d'), 's3');

        Document::create([
            'name' => $validated['name'],
            'case_id' => $validated['case_id'] ?? null,
            'created_by' => Auth::id(),
            'file_path' => $path,
            'file_type' => $request->file('file')->getClientOriginalExtension(),
            'is_generated' => false,
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }
}
