<?php

namespace App\Http\Controllers\Api\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\ChatbotDocument;
use App\Models\ChatbotDocumentAccessLog;
use App\Models\ChatbotDocumentCategory;
use App\Models\ChatbotDocumentSearchIndex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotDocumentSearchController extends Controller
{
    /**
     * Perform advanced search across chatbot documents.
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
            'type' => 'nullable|in:all,training_data,conversation_log,knowledge_base,configuration,api_documentation,user_guide,system_manual,analytics_report,error_log,performance_metrics,conversation_flow,decision_tree,fallback_response,custom_script,integration_guide',
            'category' => 'nullable|integer|exists:chatbot_document_categories,id',
            'file_format' => 'nullable|in:pdf,doc,docx,txt,json,xml,csv,jpg,png,gif,mp4,avi,zip,rar',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'uploaded_by' => 'nullable|integer|exists:users,id',
            'min_size' => 'nullable|integer|min:0',
            'max_size' => 'nullable|integer|min:0',
            'processing_status' => 'nullable|in:pending,processing,completed,failed',
            'is_public' => 'nullable|boolean',
            'sort_by' => 'nullable|in:relevance,title,created_at,file_size,download_count',
            'sort_direction' => 'nullable|in:asc,desc',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $query = $request->q;
        $limit = $request->get('limit', 50);
        $sortBy = $request->get('sort_by', 'relevance');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Build base query
        $searchQuery = ChatbotDocument::with(['category', 'uploadedBy'])
            ->active()
            ->where(function ($q) use ($query) {
                // Search in title and description
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('original_filename', 'like', "%{$query}%");
            });

        // Apply filters
        if ($request->type && $request->type !== 'all') {
            $searchQuery->where('document_type', $request->type);
        }

        if ($request->category) {
            $searchQuery->where('category_id', $request->category);
        }

        if ($request->file_format) {
            $searchQuery->where('file_format', $request->file_format);
        }

        if ($request->date_from) {
            $searchQuery->where('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $searchQuery->where('created_at', '<=', $request->date_to.' 23:59:59');
        }

        if ($request->uploaded_by) {
            $searchQuery->where('uploaded_by', $request->uploaded_by);
        }

        if ($request->min_size) {
            $searchQuery->where('file_size', '>=', $request->min_size);
        }

        if ($request->max_size) {
            $searchQuery->where('file_size', '<=', $request->max_size);
        }

        if ($request->processing_status) {
            $searchQuery->where('processing_status', $request->processing_status);
        }

        if ($request->has('is_public')) {
            $searchQuery->where('is_public', $request->boolean('is_public'));
        }

        // Search in full-text index for better results
        $fullTextResults = ChatbotDocumentSearchIndex::search($query, [
            'category_id' => $request->category,
            'document_type' => $request->type,
        ], $limit)->pluck('document_id')->toArray();

        if (! empty($fullTextResults)) {
            $searchQuery->orWhereIn('id', $fullTextResults);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'relevance':
                // For relevance, prioritize full-text matches first
                if (! empty($fullTextResults)) {
                    $searchQuery->orderByRaw('FIELD(id, '.implode(',', $fullTextResults).') DESC');
                }
                $searchQuery->orderBy('created_at', 'desc');
                break;
            case 'title':
                $searchQuery->orderBy('title', $sortDirection);
                break;
            case 'created_at':
                $searchQuery->orderBy('created_at', $sortDirection);
                break;
            case 'file_size':
                $searchQuery->orderBy('file_size', $sortDirection);
                break;
            case 'download_count':
                $searchQuery->orderBy('download_count', $sortDirection);
                break;
        }

        $results = $searchQuery->paginate($limit);

        // Add relevance scores for full-text matches
        $results->getCollection()->transform(function ($document) use ($fullTextResults, $query) {
            $document->relevance_score = in_array($document->id, $fullTextResults)
                ? ChatbotDocumentSearchIndex::where('document_id', $document->id)
                    ->first()?->calculateRelevanceScore($query) ?? 0
                : 0;

            return $document;
        });

        // Log search
        ChatbotDocumentAccessLog::logAccess(
            null,
            Auth::user(),
            'search',
            'api',
            ['query' => $query, 'filters' => $request->except('q')]
        );

        return response()->json([
            'query' => $query,
            'filters' => $request->except('q'),
            'total_results' => $results->total(),
            'results' => $results->items(),
            'pagination' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
            ],
        ]);
    }

    /**
     * Get search suggestions based on query.
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        // Get keyword suggestions from search index
        $keywords = ChatbotDocumentSearchIndex::where('is_current', true)
            ->whereRaw('JSON_CONTAINS(keywords, ?)', [json_encode($query)])
            ->orWhere('content', 'like', "%{$query}%")
            ->limit(10)
            ->pluck('keywords')
            ->flatten()
            ->filter(function ($keyword) use ($query) {
                return stripos($keyword, $query) !== false;
            })
            ->unique()
            ->take(5)
            ->values();

        // Get title suggestions
        $titles = ChatbotDocument::active()
            ->where('title', 'like', "%{$query}%")
            ->limit(5)
            ->pluck('title')
            ->map(function ($title) use ($query) {
                // Find the matching part
                $pos = stripos($title, $query);
                if ($pos !== false) {
                    return substr($title, 0, $pos + strlen($query));
                }

                return $title;
            })
            ->unique()
            ->values();

        return response()->json([
            'query' => $query,
            'suggestions' => [
                'keywords' => $keywords,
                'titles' => $titles,
                'combined' => collect([...$keywords, ...$titles])->unique()->take(8)->values(),
            ],
        ]);
    }

    /**
     * Get advanced search filters data.
     */
    public function filters()
    {
        $filters = [
            'document_types' => [
                ['value' => 'training_data', 'label' => 'Training Data'],
                ['value' => 'conversation_log', 'label' => 'Conversation Logs'],
                ['value' => 'knowledge_base', 'label' => 'Knowledge Base'],
                ['value' => 'configuration', 'label' => 'Configuration Files'],
                ['value' => 'api_documentation', 'label' => 'API Documentation'],
                ['value' => 'user_guide', 'label' => 'User Guides'],
                ['value' => 'system_manual', 'label' => 'System Manuals'],
                ['value' => 'analytics_report', 'label' => 'Analytics Reports'],
                ['value' => 'error_log', 'label' => 'Error Logs'],
                ['value' => 'performance_metrics', 'label' => 'Performance Metrics'],
                ['value' => 'conversation_flow', 'label' => 'Conversation Flows'],
                ['value' => 'decision_tree', 'label' => 'Decision Trees'],
                ['value' => 'fallback_response', 'label' => 'Fallback Responses'],
                ['value' => 'custom_script', 'label' => 'Custom Scripts'],
                ['value' => 'integration_guide', 'label' => 'Integration Guides'],
            ],
            'file_formats' => [
                ['value' => 'pdf', 'label' => 'PDF Documents'],
                ['value' => 'doc', 'label' => 'Word Documents'],
                ['value' => 'docx', 'label' => 'Word Documents'],
                ['value' => 'txt', 'label' => 'Text Files'],
                ['value' => 'json', 'label' => 'JSON Files'],
                ['value' => 'xml', 'label' => 'XML Files'],
                ['value' => 'csv', 'label' => 'CSV Files'],
                ['value' => 'jpg', 'label' => 'Images'],
                ['value' => 'png', 'label' => 'Images'],
                ['value' => 'gif', 'label' => 'Images'],
                ['value' => 'mp4', 'label' => 'Videos'],
                ['value' => 'avi', 'label' => 'Videos'],
                ['value' => 'zip', 'label' => 'Archives'],
                ['value' => 'rar', 'label' => 'Archives'],
            ],
            'processing_statuses' => [
                ['value' => 'pending', 'label' => 'Pending'],
                ['value' => 'processing', 'label' => 'Processing'],
                ['value' => 'completed', 'label' => 'Completed'],
                ['value' => 'failed', 'label' => 'Failed'],
            ],
            'categories' => ChatbotDocumentCategory::active()
                ->select('id', 'name', 'category_type')
                ->get()
                ->map(function ($category) {
                    return [
                        'value' => $category->id,
                        'label' => $category->name,
                        'type' => $category->category_type,
                    ];
                }),
            'date_ranges' => [
                ['value' => 'today', 'label' => 'Today'],
                ['value' => 'yesterday', 'label' => 'Yesterday'],
                ['value' => 'last_7_days', 'label' => 'Last 7 Days'],
                ['value' => 'last_30_days', 'label' => 'Last 30 Days'],
                ['value' => 'last_90_days', 'label' => 'Last 90 Days'],
                ['value' => 'custom', 'label' => 'Custom Range'],
            ],
        ];

        return response()->json($filters);
    }

    /**
     * Get search analytics.
     */
    public function analytics(Request $request)
    {
        $days = $request->get('days', 30);

        $analytics = [
            'popular_searches' => ChatbotDocumentAccessLog::where('action', 'search')
                ->where('accessed_at', '>=', now()->subDays($days))
                ->select('additional_data')
                ->get()
                ->map(function ($log) {
                    $data = json_decode($log->additional_data, true);

                    return $data['query'] ?? null;
                })
                ->filter()
                ->countBy()
                ->sortDesc()
                ->take(10)
                ->toArray(),

            'search_trends' => ChatbotDocumentAccessLog::where('action', 'search')
                ->where('accessed_at', '>=', now()->subDays($days))
                ->selectRaw('DATE(accessed_at) as date, COUNT(*) as searches')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('searches', 'date')
                ->toArray(),

            'top_documents' => ChatbotDocumentAccessLog::where('action', 'view')
                ->where('accessed_at', '>=', now()->subDays($days))
                ->selectRaw('document_id, COUNT(*) as views')
                ->groupBy('document_id')
                ->orderBy('views', 'desc')
                ->limit(10)
                ->with('document:id,title')
                ->get()
                ->map(function ($item) {
                    return [
                        'document_id' => $item->document_id,
                        'title' => $item->document?->title ?? 'Unknown',
                        'views' => $item->views,
                    ];
                }),

            'search_by_user' => ChatbotDocumentAccessLog::where('action', 'search')
                ->where('accessed_at', '>=', now()->subDays($days))
                ->whereNotNull('user_id')
                ->selectRaw('user_id, COUNT(*) as searches')
                ->groupBy('user_id')
                ->orderBy('searches', 'desc')
                ->limit(10)
                ->with('user:id,name,email')
                ->get()
                ->map(function ($item) {
                    return [
                        'user_id' => $item->user_id,
                        'user_name' => $item->user?->name ?? 'Unknown',
                        'searches' => $item->searches,
                    ];
                }),
        ];

        return response()->json($analytics);
    }

    /**
     * Export search results.
     */
    public function export(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
            'format' => 'required|in:csv,json,pdf',
            'limit' => 'nullable|integer|min:1|max:1000',
        ]);

        // Perform search (reuse existing search logic)
        $searchResponse = $this->search($request);
        $results = $searchResponse->getData()->results ?? [];

        switch ($request->format) {
            case 'csv':
                return $this->exportAsCsv($results);
            case 'json':
                return response()->json($results);
            case 'pdf':
                return $this->exportAsPdf($results, $request->q);
        }
    }

    protected function exportAsCsv($results)
    {
        $filename = 'document_search_results_'.date('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($results) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID', 'Title', 'Type', 'Category', 'File Format',
                'Size', 'Uploaded By', 'Upload Date', 'Status',
            ]);

            // CSV data
            foreach ($results as $result) {
                fputcsv($file, [
                    $result->id,
                    $result->title,
                    $result->document_type,
                    $result->category?->name ?? 'Uncategorized',
                    $result->file_format,
                    $this->formatBytes($result->file_size),
                    $result->uploaded_by?->name ?? 'Unknown',
                    date('Y-m-d', strtotime($result->created_at)),
                    $result->processing_status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function exportAsPdf($results, $query)
    {
        // In a real implementation, use libraries like TCPDF or DomPDF
        // For now, return a simple response
        return response()->json([
            'message' => 'PDF export would be implemented with a PDF generation library',
            'results_count' => count($results),
            'query' => $query,
        ]);
    }

    protected function formatBytes($bytes)
    {
        if ($bytes == 0) {
            return '0 Bytes';
        }

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2).' '.$sizes[$i];
    }
}
