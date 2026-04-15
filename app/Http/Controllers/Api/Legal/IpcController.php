<?php

namespace App\Http\Controllers\Api\Legal;

use App\Http\Controllers\Controller;
use App\Models\IpcSection;
use Illuminate\Http\Request;

class IpcController extends Controller
{
    /**
     * Display a listing of IPC sections.
     */
    public function index(Request $request)
    {
        $query = IpcSection::active();

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by classification
        if ($request->has('classification')) {
            $query->where('classification', $request->classification);
        }

        // Filter by cognizable
        if ($request->has('cognizable')) {
            $query->where('cognizable', $request->cognizable);
        }

        return response()->json($query->paginate(20));
    }

    /**
     * Display the specified IPC section.
     */
    public function show(IpcSection $ipcSection)
    {
        return response()->json($ipcSection->load('references'));
    }

    /**
     * Search IPC sections.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            $sections = IpcSection::active()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            foreach ($sections as $section) {
                $results[] = [
                    'id' => $section->id,
                    'type' => 'ipc',
                    'section_number' => $section->section_number,
                    'title' => $section->title,
                    'description' => substr($section->description, 0, 150).'...',
                    'classification' => $section->classification,
                    'cognizable' => $section->cognizable,
                ];
            }
        }

        return response()->json($results);
    }
}
