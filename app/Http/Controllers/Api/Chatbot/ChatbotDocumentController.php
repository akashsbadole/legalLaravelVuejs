<?php

namespace App\Http\Controllers\Api\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\ChatbotDocument;
use App\Models\ChatbotDocumentAccessLog;
use App\Models\ChatbotDocumentMetadata;
use App\Models\ChatbotDocumentSearchIndex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatbotDocumentController extends Controller
{
    /**
     * Display a listing of chatbot documents.
     */
    public function index(Request $request)
    {
        $query = ChatbotDocument::with(['category', 'uploadedBy', 'lastModifiedBy'])
            ->active();

        // Filters
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('document_type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('processing_status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $allowedSorts = ['created_at', 'title', 'file_size', 'processing_status'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $documents = $query->paginate(20);

        // Log access
        ChatbotDocumentAccessLog::logAccess(
            null,
            Auth::user(),
            'search',
            'api',
            ['query' => $request->all()]
        );

        return response()->json($documents);
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:'.(config('ai.document_processing.max_file_size', 10485760) / 1024),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:chatbot_document_categories,id',
            'document_type' => 'required|in:training_data,conversation_log,knowledge_base,configuration,api_documentation,user_guide,system_manual,analytics_report,error_log,performance_metrics,conversation_flow,decision_tree,fallback_response,custom_script,integration_guide',
            'is_public' => 'boolean',
        ]);

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();
        $fileExtension = strtolower($file->getClientOriginalExtension());

        // Generate unique filename
        $filename = Str::uuid().'.'.$fileExtension;
        $filePath = 'chatbot-documents/'.date('Y/m').'/'.$filename;

        // Store file
        $storedPath = $file->storeAs('chatbot-documents/'.date('Y/m'), $filename, 'public');

        if (! $storedPath) {
            return response()->json(['error' => 'Failed to store file'], 500);
        }

        // Create document record
        $document = ChatbotDocument::create([
            'title' => $request->title,
            'description' => $request->description,
            'document_type' => $request->document_type,
            'file_format' => $fileExtension,
            'original_filename' => $originalFilename,
            'file_path' => $storedPath,
            'file_url' => Storage::url($storedPath),
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'category_id' => $request->category_id,
            'uploaded_by' => Auth::id(),
            'processing_status' => 'pending',
            'is_public' => $request->is_public ?? false,
        ]);

        // Log upload
        ChatbotDocumentAccessLog::logAccess(
            $document->id,
            Auth::user(),
            'upload',
            'api',
            ['filename' => $originalFilename, 'size' => $fileSize]
        );

        // Dispatch processing job
        if ($request->boolean('auto_process', true)) {
            // In a real implementation, dispatch a job for processing
            $this->processDocument($document);
        }

        return response()->json($document->load(['category', 'uploadedBy']), 201);
    }

    /**
     * Display the specified document.
     */
    public function show(ChatbotDocument $document)
    {
        // Check access permissions
        if (! $document->canBeAccessedBy(Auth::user()) && ! $document->is_public) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Log access
        ChatbotDocumentAccessLog::logAccess(
            $document->id,
            Auth::user(),
            'view',
            'api'
        );

        $document->incrementViewCount();

        return response()->json($document->load([
            'category',
            'uploadedBy',
            'lastModifiedBy',
            'metadata',
            'searchIndex',
        ]));
    }

    /**
     * Update the specified document.
     */
    public function update(Request $request, ChatbotDocument $document)
    {
        // Check permissions (only uploader or admin can edit)
        if ($document->uploaded_by !== Auth::id() && ! Auth::user()->is_admin) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:chatbot_document_categories,id',
            'document_type' => 'sometimes|required|in:training_data,conversation_log,knowledge_base,configuration,api_documentation,user_guide,system_manual,analytics_report,error_log,performance_metrics,conversation_flow,decision_tree,fallback_response,custom_script,integration_guide',
            'is_public' => 'boolean',
        ]);

        $document->update(array_merge($request->only([
            'title', 'description', 'category_id', 'document_type', 'is_public',
        ]), [
            'last_modified_by' => Auth::id(),
        ]));

        // Log update
        ChatbotDocumentAccessLog::logAccess(
            $document->id,
            Auth::user(),
            'edit',
            'api',
            ['changes' => $request->only(['title', 'description', 'category_id', 'document_type', 'is_public'])]
        );

        return response()->json($document->load(['category', 'uploadedBy', 'lastModifiedBy']));
    }

    /**
     * Remove the specified document.
     */
    public function destroy(ChatbotDocument $document)
    {
        // Check permissions (only uploader or admin can delete)
        if ($document->uploaded_by !== Auth::id() && ! Auth::user()->is_admin) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Delete file from storage
        if ($document->file_path && Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        // Log deletion
        ChatbotDocumentAccessLog::logAccess(
            $document->id,
            Auth::user(),
            'delete',
            'api',
            ['filename' => $document->original_filename]
        );

        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }

    /**
     * Download the document file.
     */
    public function download(ChatbotDocument $document)
    {
        // Check access permissions
        if (! $document->canBeAccessedBy(Auth::user()) && ! $document->is_public) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        if (! $document->file_path || ! Storage::exists($document->file_path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Log download
        ChatbotDocumentAccessLog::logAccess(
            $document->id,
            Auth::user(),
            'download',
            'api'
        );

        $document->incrementDownloadCount();

        return Storage::download($document->file_path, $document->original_filename);
    }

    /**
     * Get document statistics.
     */
    public function stats()
    {
        $stats = [
            'total_documents' => ChatbotDocument::active()->count(),
            'by_type' => ChatbotDocument::active()
                ->selectRaw('document_type, COUNT(*) as count')
                ->groupBy('document_type')
                ->pluck('count', 'document_type')
                ->toArray(),
            'by_status' => ChatbotDocument::active()
                ->selectRaw('processing_status, COUNT(*) as count')
                ->groupBy('processing_status')
                ->pluck('count', 'processing_status')
                ->toArray(),
            'storage_used' => ChatbotDocument::active()->sum('file_size'),
            'recent_uploads' => ChatbotDocument::active()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'title', 'created_at', 'file_size']),
        ];

        return response()->json($stats);
    }

    /**
     * Process document (extract text, generate thumbnails, etc.)
     */
    protected function processDocument(ChatbotDocument $document)
    {
        try {
            $document->update(['processing_status' => 'processing']);

            // Extract text based on file type
            $extractedText = $this->extractTextFromFile($document);

            if ($extractedText) {
                $document->update([
                    'extracted_text' => $extractedText,
                    'processing_status' => 'completed',
                    'processed_at' => now(),
                    'is_indexed' => true,
                ]);

                // Create search index
                ChatbotDocumentSearchIndex::updateForDocument($document, $extractedText, [
                    'keywords' => $this->extractKeywords($extractedText),
                    'entities' => $this->extractEntities($extractedText),
                ]);

                // Add metadata
                $this->addDocumentMetadata($document, $extractedText);
            } else {
                $document->update([
                    'processing_status' => 'completed',
                    'processed_at' => now(),
                    'is_indexed' => true,
                ]);
            }

        } catch (\Exception $e) {
            $document->update([
                'processing_status' => 'failed',
                'processing_error' => $e->getMessage(),
            ]);

            \Log::error('Document processing failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Extract text from uploaded file.
     */
    protected function extractTextFromFile(ChatbotDocument $document): ?array
    {
        $filePath = Storage::path($document->file_path);

        try {
            switch ($document->file_format) {
                case 'pdf':
                    return $this->extractTextFromPDF($filePath);
                case 'docx':
                case 'doc':
                    return $this->extractTextFromWord($filePath);
                case 'txt':
                    return $this->extractTextFromTxt($filePath);
                case 'jpg':
                case 'png':
                case 'gif':
                    return $this->extractTextFromImage($filePath);
                default:
                    return ['content' => 'Text extraction not supported for this file type'];
            }
        } catch (\Exception $e) {
            \Log::error('Text extraction failed', [
                'document_id' => $document->id,
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);

            return ['content' => 'Text extraction failed', 'error' => $e->getMessage()];
        }
    }

    /**
     * Extract text from PDF files.
     */
    protected function extractTextFromPDF(string $filePath): array
    {
        // In a real implementation, use libraries like Smalot\PdfParser or similar
        // For now, return mock data
        return [
            'content' => 'PDF text extraction would be implemented here using a PDF parsing library.',
            'page_count' => 10,
            'word_count' => 1500,
        ];
    }

    /**
     * Extract text from Word documents.
     */
    protected function extractTextFromWord(string $filePath): array
    {
        // In a real implementation, use PhpOffice/PhpWord
        return [
            'content' => 'Word document text extraction would be implemented here.',
            'word_count' => 800,
        ];
    }

    /**
     * Extract text from plain text files.
     */
    protected function extractTextFromTxt(string $filePath): array
    {
        $content = file_get_contents($filePath);

        return [
            'content' => $content,
            'word_count' => str_word_count($content),
        ];
    }

    /**
     * Extract text from images using OCR.
     */
    protected function extractTextFromImage(string $filePath): array
    {
        // In a real implementation, use Tesseract OCR or Google Vision AI
        return [
            'content' => 'OCR text extraction would be implemented here for images.',
            'confidence' => 0.85,
        ];
    }

    /**
     * Extract keywords from text.
     */
    protected function extractKeywords(string $text): array
    {
        // Simple keyword extraction - in production, use NLP libraries
        $words = str_word_count(strtolower($text), 1);
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by'];

        $keywords = array_filter($words, function ($word) use ($stopWords) {
            return strlen($word) > 3 && ! in_array($word, $stopWords);
        });

        return array_slice(array_unique($keywords), 0, 20);
    }

    /**
     * Extract named entities from text.
     */
    protected function extractEntities(string $text): array
    {
        // Simple entity extraction - in production, use NLP libraries
        $entities = [];

        // Look for potential organization names (capitalized words)
        preg_match_all('/\b[A-Z][a-z]+(?:\s+[A-Z][a-z]+)*\b/', $text, $matches);
        $entities = array_merge($entities, array_slice($matches[0], 0, 10));

        return array_unique($entities);
    }

    /**
     * Add metadata to document.
     */
    protected function addDocumentMetadata(ChatbotDocument $document, array $extractedData): void
    {
        $metadata = [
            ['key' => 'word_count', 'value' => $extractedData['word_count'] ?? 0, 'value_type' => 'number'],
            ['key' => 'page_count', 'value' => $extractedData['page_count'] ?? 1, 'value_type' => 'number'],
            ['key' => 'processing_date', 'value' => now()->toISOString(), 'value_type' => 'date'],
            ['key' => 'has_ocr', 'value' => in_array($document->file_format, ['jpg', 'png', 'pdf']), 'value_type' => 'boolean'],
        ];

        foreach ($metadata as $item) {
            ChatbotDocumentMetadata::setForDocument(
                $document->id,
                $item['key'],
                $item['value'],
                $item['value_type']
            );
        }
    }
}
