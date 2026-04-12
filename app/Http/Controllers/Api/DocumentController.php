<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Document::with(['category', 'case', 'createdBy']);

        if ($request->filled('category')) {
            $query->where('document_category_id', $request->category);
        }

        if ($request->filled('case')) {
            $query->where('case_id', $request->case);
        }

        $documents = $query->paginate($request->get('per_page', 15));

        return response()->json($documents);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'document_category_id' => 'required|exists:document_categories,id',
            'case_id' => 'nullable|exists:cases,id',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'description' => 'nullable|string',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $document = Document::create([
            'title' => $validated['title'],
            'document_category_id' => $validated['document_category_id'],
            'case_id' => $validated['case_id'] ?? null,
            'file_path' => $path,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_size' => $request->file('file')->getSize(),
            'mime_type' => $request->file('file')->getMimeType(),
            'created_by' => auth()->id(),
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json(['data' => $document, 'message' => 'Document uploaded'], 201);
    }

    public function show(Document $document): JsonResponse
    {
        $document->load(['category', 'case', 'createdBy']);

        return response()->json(['data' => $document]);
    }

    public function download(Document $document): JsonResponse
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->json([
            'download_url' => route('api.documents.download', $document),
            'file_name' => $document->file_name,
        ]);
    }

    public function destroy(Document $document): JsonResponse
    {
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json(['message' => 'Document deleted']);
    }
}
