<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = LegalCase::with(['status', 'assignedAttorney', 'client']);

        if ($request->filled('status')) {
            $query->where('case_status_id', $request->status);
        }

        if ($request->filled('attorney')) {
            $query->where('assigned_attorney_id', $request->attorney);
        }

        $cases = $query->paginate($request->get('per_page', 15));

        return response()->json($cases);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'case_number' => 'required|string|unique:cases,case_number',
            'case_status_id' => 'required|exists:case_statuses,id',
            'assigned_attorney_id' => 'required|exists:users,id',
            'client_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'filing_date' => 'nullable|date',
            'court_name' => 'nullable|string',
            'case_type' => 'nullable|string',
        ]);

        $case = LegalCase::create($validated);

        return response()->json(['data' => $case, 'message' => 'Case created'], 201);
    }

    public function show(LegalCase $legalCase): JsonResponse
    {
        $legalCase->load(['status', 'assignedAttorney', 'client', 'participants', 'notes', 'tasks']);

        return response()->json(['data' => $legalCase]);
    }

    public function update(Request $request, LegalCase $legalCase): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'case_status_id' => 'sometimes|exists:case_statuses,id',
            'assigned_attorney_id' => 'sometimes|exists:users,id',
            'description' => 'nullable|string',
            'filing_date' => 'nullable|date',
            'court_name' => 'nullable|string',
            'case_type' => 'nullable|string',
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
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return response()->json(['data' => $note, 'message' => 'Note added'], 201);
    }
}
