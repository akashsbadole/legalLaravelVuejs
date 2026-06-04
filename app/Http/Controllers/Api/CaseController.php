<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CaseStatus;
use App\Models\Document;
use App\Models\LegalCase;
use App\Services\AI\AIServiceManager;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function store(Request $request, NotificationService $notifications): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'case_type' => 'nullable|string',
            'status_id' => 'nullable|exists:case_statuses,id',
            'client_id' => 'required|exists:users,id',
            'assigned_attorney_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'open_date' => 'nullable|date',
            'expected_close_date' => 'nullable|date',
            'estimated_value' => 'nullable|numeric',
        ]);

        $validated['case_number'] = LegalCase::generateCaseNumber();
        $validated['status_id'] = $validated['status_id'] ?? CaseStatus::where('is_active', true)->first()?->id;
        $validated['open_date'] = $validated['open_date'] ?? now();
        $validated['assigned_attorney_id'] = $validated['assigned_attorney_id'] ?? auth()->id();

        $case = LegalCase::create($validated);

        if ($case->assigned_attorney_id) {
            $notifications->notifyNewCase($case->assigned_attorney_id, $case->toArray());
        }

        return response()->json(['data' => $case->load(['status', 'assignedAttorney', 'client']), 'message' => 'Case created'], 201);
    }

    public function show(LegalCase $legalCase): JsonResponse
    {
        $legalCase->load(['status', 'assignedAttorney', 'client', 'tasks', 'notes', 'documents', 'timelineEvents']);

        return response()->json($legalCase);
    }

    public function update(Request $request, LegalCase $legalCase): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'status_id' => 'sometimes|exists:case_statuses,id',
            'case_type' => 'nullable|string',
            'client_id' => 'sometimes|exists:users,id',
            'assigned_attorney_id' => 'sometimes|exists:users,id',
            'description' => 'nullable|string',
            'open_date' => 'nullable|date',
            'expected_close_date' => 'nullable|date',
            'actual_close_date' => 'nullable|date',
            'estimated_value' => 'nullable|numeric',
        ]);

        $legalCase->update($validated);

        return response()->json(['data' => $legalCase->load(['status', 'assignedAttorney', 'client']), 'message' => 'Case updated']);
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

    public function aiAnalysis(LegalCase $case, AIServiceManager $aiManager): JsonResponse
    {
        $content = "Case Title: {$case->title}\nDescription: {$case->description}\nType: {$case->case_type}";

        try {
            $analysis = $aiManager->analyzeDocument($content, [
                'focus_areas' => ['legal citations', 'precedents', 'winning arguments', 'Indian law'],
            ]);

            $precedents = $aiManager->findPrecedents($content);

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'precedents' => $precedents,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI Analysis failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function aiGenerateDocument(Request $request, AIServiceManager $aiManager): JsonResponse
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'document_type' => 'required|string', // e.g., "Affidavit", "Legal Notice", "Power of Attorney"
            'additional_instructions' => 'nullable|string',
        ]);

        $case = LegalCase::with(['client', 'assignedAttorney'])->findOrFail($validated['case_id']);

        $userData = [
            'client_name' => $case->client->name ?? 'N/A',
            'client_address' => $case->client->address ?? 'N/A',
            'attorney_name' => $case->assignedAttorney->name ?? 'N/A',
            'case_number' => $case->case_number,
            'case_title' => $case->title,
            'case_description' => $case->description,
            'case_type' => $case->case_type,
            'current_date' => now()->format('F d, Y'),
        ];

        $templateData = [
            'type' => $validated['document_type'],
            'structure' => "Standard Indian {$validated['document_type']} format",
        ];

        if ($validated['additional_instructions']) {
            $templateData['additional_instructions'] = $validated['additional_instructions'];
        }

        try {
            $content = $aiManager->generateContent($templateData, $userData);

            if (empty($content)) {
                return response()->json(['success' => false, 'message' => 'AI failed to generate content'], 500);
            }

            $document = Document::create([
                'name' => $validated['document_type'].'_'.$case->case_number.'_'.now()->format('YmdHis'),
                'case_id' => $case->id,
                'created_by' => auth()->id(),
                'file_path' => 'documents/ai/'.now()->format('Y/m/d').'/'.uniqid().'.html',
                'file_type' => 'html',
                'is_generated' => true,
            ]);

            Storage::disk('public')->put($document->file_path, $content);

            return response()->json([
                'success' => true,
                'data' => $document,
                'content' => $content,
                'message' => 'Document generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI Generation failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
