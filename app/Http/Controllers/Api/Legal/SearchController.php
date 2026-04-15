<?php

namespace App\Http\Controllers\Api\Legal;

use App\Http\Controllers\Controller;
use App\Models\ConstitutionArticle;
use App\Models\CpcSection;
use App\Models\CrpcSection;
use App\Models\IpcSection;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all'); // ipc, crpc, cpc, constitution, all
        $limit = $request->get('limit', 20);

        if (strlen($query) < 2) {
            return response()->json(['results' => [], 'message' => 'Search query must be at least 2 characters']);
        }

        $results = [];

        // Search IPC sections
        if ($type === 'all' || $type === 'ipc') {
            $ipcResults = IpcSection::active()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->limit($limit)
                ->get()
                ->map(function ($section) {
                    return [
                        'id' => $section->id,
                        'type' => 'ipc',
                        'section_number' => $section->section_number,
                        'title' => $section->title,
                        'description' => substr($section->description, 0, 200).'...',
                        'classification' => $section->classification,
                        'cognizable' => $section->cognizable,
                        'url' => "/api/legal/ipc/{$section->id}",
                    ];
                });

            $results = array_merge($results, $ipcResults->toArray());
        }

        // Search CrPC sections
        if ($type === 'all' || $type === 'crpc') {
            $crpcResults = CrpcSection::active()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('procedure', 'like', "%{$query}%")
                ->limit($limit)
                ->get()
                ->map(function ($section) {
                    return [
                        'id' => $section->id,
                        'type' => 'crpc',
                        'section_number' => $section->section_number,
                        'title' => $section->title,
                        'description' => substr($section->description, 0, 200).'...',
                        'court_level' => $section->court_level,
                        'url' => "/api/legal/crpc/{$section->id}",
                    ];
                });

            $results = array_merge($results, $crpcResults->toArray());
        }

        // Search CPC sections
        if ($type === 'all' || $type === 'cpc') {
            $cpcResults = CpcSection::active()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('procedure', 'like', "%{$query}%")
                ->limit($limit)
                ->get()
                ->map(function ($section) {
                    return [
                        'id' => $section->id,
                        'type' => 'cpc',
                        'section_number' => $section->section_number,
                        'title' => $section->title,
                        'description' => substr($section->description, 0, 200).'...',
                        'court_level' => $section->court_level,
                        'url' => "/api/legal/cpc/{$section->id}",
                    ];
                });

            $results = array_merge($results, $cpcResults->toArray());
        }

        // Search Constitution articles
        if ($type === 'all' || $type === 'constitution') {
            $constitutionResults = ConstitutionArticle::active()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->orWhere('explanation', 'like', "%{$query}%")
                ->limit($limit)
                ->get()
                ->map(function ($article) {
                    return [
                        'id' => $article->id,
                        'type' => 'constitution',
                        'article_number' => $article->article_number,
                        'title' => $article->title,
                        'description' => substr($article->content, 0, 200).'...',
                        'part' => $article->part,
                        'category' => $article->category,
                        'url' => "/api/legal/constitution/{$article->id}",
                    ];
                });

            $results = array_merge($results, $constitutionResults->toArray());
        }

        // Sort results by relevance (this is a simple implementation)
        usort($results, function ($a, $b) {
            return strcmp($a['title'], $b['title']);
        });

        return response()->json([
            'query' => $query,
            'type' => $type,
            'total_results' => count($results),
            'results' => array_slice($results, 0, $limit),
        ]);
    }

    /**
     * Advanced search with filters.
     */
    public function advanced(Request $request)
    {
        $query = $request->get('q', '');
        $filters = $request->get('filters', []);

        if (strlen($query) < 2) {
            return response()->json(['results' => [], 'message' => 'Search query must be at least 2 characters']);
        }

        $results = [];

        // IPC search with filters
        if (! isset($filters['exclude_ipc']) || ! $filters['exclude_ipc']) {
            $ipcQuery = IpcSection::active()->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });

            if (isset($filters['classification'])) {
                $ipcQuery->where('classification', $filters['classification']);
            }

            if (isset($filters['cognizable'])) {
                $ipcQuery->where('cognizable', $filters['cognizable']);
            }

            $ipcResults = $ipcQuery->limit(10)->get()->map(function ($section) {
                return [
                    'id' => $section->id,
                    'type' => 'ipc',
                    'section_number' => $section->section_number,
                    'title' => $section->title,
                    'description' => substr($section->description, 0, 150).'...',
                    'metadata' => [
                        'classification' => $section->classification,
                        'cognizable' => $section->cognizable,
                    ],
                ];
            });

            $results['ipc'] = $ipcResults;
        }

        // Similar logic for other legal databases...

        return response()->json([
            'query' => $query,
            'filters' => $filters,
            'results' => $results,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
