<?php

namespace App\Http\Controllers\Api\Legal;

use App\Http\Controllers\Controller;
use App\Models\ConstitutionArticle;
use Illuminate\Http\Request;

class ConstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ConstitutionArticle::active();

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('explanation', 'like', "%{$search}%");
            });
        }

        // Filter by part
        if ($request->has('part')) {
            $query->where('part', $request->part);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->paginate(20));
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
    public function show(ConstitutionArticle $constitutionArticle)
    {
        return response()->json($constitutionArticle->load('references'));
    }

    /**
     * Search Constitution articles.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            $articles = ConstitutionArticle::active()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->orWhere('explanation', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            foreach ($articles as $article) {
                $results[] = [
                    'id' => $article->id,
                    'type' => 'constitution',
                    'article_number' => $article->article_number,
                    'title' => $article->title,
                    'description' => substr($article->content, 0, 150).'...',
                    'part' => $article->part,
                    'category' => $article->category,
                ];
            }
        }

        return response()->json($results);
    }

    /**
     * Get articles by category.
     */
    public function byCategory($category)
    {
        $articles = ConstitutionArticle::active()
            ->where('category', $category)
            ->get();

        return response()->json($articles);
    }

    /**
     * Get articles by part.
     */
    public function byPart($part)
    {
        $articles = ConstitutionArticle::active()
            ->where('part', $part)
            ->orderBy('article_number')
            ->get();

        return response()->json($articles);
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
