<?php

namespace App\Http\Controllers\Api\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\ChatbotDocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotDocumentCategoryController extends Controller
{
    /**
     * Display a listing of document categories.
     */
    public function index(Request $request)
    {
        $query = ChatbotDocumentCategory::active();

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('category_type', $request->type);
        }

        if ($request->boolean('tree', false)) {
            // Return hierarchical structure
            $categories = $query->ordered()->get();
            $tree = $this->buildCategoryTree($categories);

            return response()->json($tree);
        }

        $categories = $query->ordered()->get();

        return response()->json($categories);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        // Only admins can create categories
        if (! Auth::user()->is_admin) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:chatbot_document_categories',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'category_type' => 'required|in:training,configuration,documentation,analytics,logs,flows,scripts,guides,other',
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'string',
            'max_file_size' => 'nullable|integer|min:1024|max:104857600', // 1KB to 100MB
            'auto_process' => 'boolean',
            'requires_approval' => 'boolean',
            'parent_id' => 'nullable|exists:chatbot_document_categories,id',
        ]);

        $category = ChatbotDocumentCategory::create(array_merge($request->all(), [
            'document_count' => 0,
        ]));

        return response()->json($category, 201);
    }

    /**
     * Display the specified category.
     */
    public function show(ChatbotDocumentCategory $category)
    {
        return response()->json($category->load(['documents' => function ($query) {
            $query->active()->limit(10);
        }, 'children']));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, ChatbotDocumentCategory $category)
    {
        // Only admins can update categories
        if (! Auth::user()->is_admin) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:chatbot_document_categories,slug,'.$category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'category_type' => 'sometimes|required|in:training,configuration,documentation,analytics,logs,flows,scripts,guides,other',
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'string',
            'max_file_size' => 'nullable|integer|min:1024|max:104857600',
            'auto_process' => 'boolean',
            'requires_approval' => 'boolean',
            'parent_id' => 'nullable|exists:chatbot_document_categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update($request->all());

        return response()->json($category);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(ChatbotDocumentCategory $category)
    {
        // Only admins can delete categories
        if (! Auth::user()->is_admin) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Check if category has documents
        if ($category->document_count > 0) {
            return response()->json(['error' => 'Cannot delete category with documents'], 400);
        }

        // Check if category has children
        if ($category->children()->exists()) {
            return response()->json(['error' => 'Cannot delete category with subcategories'], 400);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    /**
     * Get documents in a category.
     */
    public function documents(ChatbotDocumentCategory $category, Request $request)
    {
        $documents = $category->documents()
            ->active()
            ->with(['uploadedBy', 'lastModifiedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($documents);
    }

    /**
     * Get category statistics.
     */
    public function stats(ChatbotDocumentCategory $category)
    {
        $stats = [
            'total_documents' => $category->document_count,
            'documents_by_type' => $category->documents()
                ->active()
                ->selectRaw('document_type, COUNT(*) as count')
                ->groupBy('document_type')
                ->pluck('count', 'document_type')
                ->toArray(),
            'documents_by_status' => $category->documents()
                ->active()
                ->selectRaw('processing_status, COUNT(*) as count')
                ->groupBy('processing_status')
                ->pluck('count', 'processing_status')
                ->toArray(),
            'total_size' => $category->documents()->active()->sum('file_size'),
            'recent_uploads' => $category->documents()
                ->active()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'title', 'created_at', 'file_size']),
            'subcategories' => $category->children()
                ->active()
                ->withCount('documents')
                ->get(['id', 'name', 'document_count']),
        ];

        return response()->json($stats);
    }

    /**
     * Get category tree structure.
     */
    public function tree()
    {
        $categories = ChatbotDocumentCategory::active()->ordered()->get();
        $tree = $this->buildCategoryTree($categories);

        return response()->json($tree);
    }

    /**
     * Bulk update categories.
     */
    public function bulkUpdate(Request $request)
    {
        // Only admins can perform bulk operations
        if (! Auth::user()->is_admin) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'integer|exists:chatbot_document_categories,id',
            'action' => 'required|in:activate,deactivate,delete,set_type',
            'type' => 'required_if:action,set_type|in:training,configuration,documentation,analytics,logs,flows,scripts,guides,other',
        ]);

        $categoryIds = $request->category_ids;

        switch ($request->action) {
            case 'activate':
                ChatbotDocumentCategory::whereIn('id', $categoryIds)->update(['is_active' => true]);
                break;
            case 'deactivate':
                ChatbotDocumentCategory::whereIn('id', $categoryIds)->update(['is_active' => false]);
                break;
            case 'delete':
                // Check if categories can be deleted
                $categoriesWithContent = ChatbotDocumentCategory::whereIn('id', $categoryIds)
                    ->where(function ($query) {
                        $query->where('document_count', '>', 0)
                            ->orWhereHas('children');
                    })
                    ->exists();

                if ($categoriesWithContent) {
                    return response()->json(['error' => 'Some categories contain documents or subcategories'], 400);
                }

                ChatbotDocumentCategory::whereIn('id', $categoryIds)->delete();
                break;
            case 'set_type':
                ChatbotDocumentCategory::whereIn('id', $categoryIds)->update(['category_type' => $request->type]);
                break;
        }

        return response()->json(['message' => 'Bulk operation completed successfully']);
    }

    /**
     * Build hierarchical category tree.
     */
    protected function buildCategoryTree($categories)
    {
        $tree = [];
        $categoriesById = $categories->keyBy('id');

        foreach ($categories as $category) {
            if ($category->parent_id) {
                // Add as child to parent
                if (isset($categoriesById[$category->parent_id])) {
                    $categoriesById[$category->parent_id]->children = $categoriesById[$category->parent_id]->children ?? collect();
                    $categoriesById[$category->parent_id]->children->push($category);
                }
            } else {
                // Add to root level
                $tree[] = $category;
            }
        }

        return $tree;
    }
}
