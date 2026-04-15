<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CaseStatus;
use App\Models\LegalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = LegalCase::with(['status', 'assignedAttorney', 'client']);

        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('case_type', $request->type);
        }

        $cases = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json($cases);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'case_type' => 'nullable|string',
            'status_id' => 'nullable|exists:case_statuses,id',
            'description' => 'nullable|string',
        ]);

        $validated['case_number'] = LegalCase::generateCaseNumber();
        $validated['case_status_id'] = $validated['status_id'] ?? CaseStatus::first()?->id;
        $validated['open_date'] = now();

        $case = LegalCase::create($validated);

        return response()->json(['data' => $case, 'message' => 'Case created'], 201);
    }

    public function show(LegalCase $legalCase): JsonResponse
    {
        $legalCase->load(['status', 'assignedAttorney', 'client', 'tasks']);

        return response()->json($legalCase);
    }

    public function update(Request $request, LegalCase $legalCase): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'case_status_id' => 'sometimes|exists:case_statuses,id',
            'case_type' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $legalCase->update($validated);

        return response()->json(['data' => $legalCase, 'message' => 'Case updated']);
    }

    public function destroy(LegalCase $legalCase): JsonResponse
    {
        $legalCase->delete();

        return response()->json(['message' => 'Case deleted']);
    }

    public function timeline(LegalCase $legalCase): JsonResponse
    {
        $timeline = $legalCase->timelineEvents()->orderBy('event_date', 'desc')->get();

        return response()->json(['data' => $timeline]);
    }

    public function addNote(Request $request, LegalCase $legalCase): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $note = $legalCase->notes()->create([
            'user_id' => auth()->id,
            'content' => $validated['content'],
        ]);

        return response()->json(['data' => $note, 'message' => 'Note added'], 201);
    }
}
