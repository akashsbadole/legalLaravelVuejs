<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalCase;
use App\Models\User;
use App\Models\Document;
use App\Models\CaseTask;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GlobalSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->get("q", "");
        $type = $request->get("type", "all");
        $limit = $request->get("limit", 20);

        if (strlen($query) < 2) {
            return response()->json(["results" => [], "message" => "Search query must be at least 2 characters"]);
        }

        $results = [];

        if ($type === "all" || $type === "cases") {
            $cases = LegalCase::with(["client", "status"])
                ->where("title", "like", "%{$query}%")
                ->orWhere("case_number", "like", "%{$query}%")
                ->orWhere("description", "like", "%{$query}%")
                ->limit($limit)
                ->get()
                ->map(fn($c) => [
                    "id" => $c->id,
                    "type" => "case",
                    "title" => $c->title,
                    "case_number" => $c->case_number,
                    "description" => $c->status?->name ?? "Active",
                    "date" => $c->created_at->toDateString(),
                    "status" => $c->status?->name ?? "Active"
                ]);
            $results = array_merge($results, $cases->toArray());
        }

        if ($type === "all" || $type === "clients") {
            $clients = User::where("role", "client")
                ->where("name", "like", "%{$query}%")
                ->orWhere("email", "like", "%{$query}%")
                ->limit($limit)
                ->get()
                ->map(fn($c) => [
                    "id" => $c->id,
                    "type" => "client",
                    "title" => $c->name,
                    "description" => "Client since " . $c->created_at->format("Y"),
                    "date" => $c->created_at->toDateString()
                ]);
            $results = array_merge($results, $clients->toArray());
        }

        if ($type === "all" || $type === "documents") {
            $documents = Document::where("name", "like", "%{$query}%")
                ->orWhere("description", "like", "%{$query}%")
                ->limit($limit)
                ->get()
                ->map(fn($d) => [
                    "id" => $d->id,
                    "type" => "document",
                    "title" => $d->name,
                    "description" => "Last modified " . $d->updated_at->diffForHumans(),
                    "date" => $d->created_at->toDateString()
                ]);
            $results = array_merge($results, $documents->toArray());
        }

        if ($type === "all" || $type === "tasks") {
            $tasks = CaseTask::with("case")
                ->where("title", "like", "%{$query}%")
                ->orWhere("description", "like", "%{$query}%")
                ->limit($limit)
                ->get()
                ->map(fn($t) => [
                    "id" => $t->id,
                    "type" => "task",
                    "title" => $t->title,
                    "description" => $t->due_date ? "Due: " . $t->due_date : "No due date",
                    "date" => $t->due_date ?? $t->created_at->toDateString()
                ]);
            $results = array_merge($results, $tasks->toArray());
        }

        return response()->json([
            "query" => $query,
            "type" => $type,
            "total_results" => count($results),
            "results" => array_slice($results, 0, $limit)
        ]);
    }

    public function recentSearches(Request $request): JsonResponse
    {
        $recentSearches = $request->session()->get("recent_searches", []);
        return response()->json(["recent_searches" => $recentSearches]);
    }

    public function saveSearch(Request $request): JsonResponse
    {
        $request->validate(["term" => "required|string|max:255"]);
        
        $term = $request->term;
        $recentSearches = $request->session()->get("recent_searches", []);
        
        $recentSearches = array_filter($recentSearches, fn($s) => $s["term"] !== $term);
        
        array_unshift($recentSearches, [
            "id" => time(),
            "term" => $term,
            "count" => 1
        ]);
        
        $recentSearches = array_slice($recentSearches, 0, 10);
        
        $request->session()->put("recent_searches", $recentSearches);
        
        return response()->json(["message" => "Search saved", "recent_searches" => $recentSearches]);
    }
}