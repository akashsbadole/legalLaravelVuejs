<?php

namespace App\Http\Controllers\Api\Legal;

use App\Http\Controllers\Controller;
use App\Models\CrpcSection;
use Illuminate\Http\Request;

class CrpcController extends Controller
{
    /**
     * Display a listing of CrPC sections.
     */
    public function index(Request $request)
    {
        $query = CrpcSection::active();

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

        return response()->json($query->paginate(20));
    }

    /**
     * Display the specified CrPC section.
     */
    public function show(CrpcSection $crpcSection)
    {
        return response()->json($crpcSection->load('references'));
    }

    /**
     * Search CrPC sections.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            $sections = CrpcSection::active()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('procedure', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            foreach ($sections as $section) {
                $results[] = [
                    'id' => $section->id,
                    'type' => 'crpc',
                    'section_number' => $section->section_number,
                    'title' => $section->title,
                    'description' => substr($section->description, 0, 150).'...',
                    'court_level' => $section->court_level,
                ];
            }
        }

        return response()->json($results);
    }
}
