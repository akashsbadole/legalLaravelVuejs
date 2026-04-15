<?php

namespace App\Http\Controllers\Api\Legal;

use App\Http\Controllers\Controller;
use App\Models\CpcSection;
use Illuminate\Http\Request;

class CpcController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CpcSection::active();

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('procedure', 'like', "%{$search}%");
            });
        }

        // Filter by court level
        if ($request->has('court_level')) {
            $query->where('court_level', $request->court_level);
        }

        // Filter by monetary jurisdiction
        if ($request->has('min_amount')) {
            $query->where('monetary_jurisdiction', '>=', $request->min_amount);
        }

        if ($request->has('max_amount')) {
            $query->where('monetary_jurisdiction', '<=', $request->max_amount);
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
    public function show(CpcSection $cpcSection)
    {
        return response()->json($cpcSection->load('references'));
    }

    /**
     * Search CPC sections.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            $sections = CpcSection::active()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('procedure', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            foreach ($sections as $section) {
                $results[] = [
                    'id' => $section->id,
                    'type' => 'cpc',
                    'section_number' => $section->section_number,
                    'title' => $section->title,
                    'description' => substr($section->description, 0, 150).'...',
                    'court_level' => $section->court_level,
                ];
            }
        }

        return response()->json($results);
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
