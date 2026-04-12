<?php

namespace App\Http\Controllers;

use App\Models\LegalCase;
use App\Models\CaseStatus;
use App\Models\CaseNote;
use App\Models\CaseParticipant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CaseController extends Controller
{
    public function index(Request $request): View
    {
        $query = LegalCase::with(['status', 'assignedAttorney']);

        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        if ($request->filled('attorney')) {
            $query->where('assigned_attorney_id', $request->attorney);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('case_number', 'like', "%{$request->search}%");
            });
        }

        $cases = $query->orderBy('created_at', 'desc')->paginate(15);
        $statuses = CaseStatus::all();

        return view('cases.index', compact('cases', 'statuses'));
    }

    public function create(): View
    {
        $statuses = CaseStatus::where('is_active', true)->get();
        return view('cases.create', compact('statuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'case_type' => 'nullable|string|max:100',
            'status_id' => 'required|exists:case_statuses,id',
            'assigned_attorney_id' => 'required|exists:users,id',
            'open_date' => 'required|date',
            'expected_close_date' => 'nullable|date',
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['case_number'] = LegalCase::generateCaseNumber();

        $case = LegalCase::create($validated);

        return redirect()->route('cases.show', $case)->with('success', 'Case created successfully.');
    }

    public function show(LegalCase $case): View
    {
        $case->load(['status', 'assignedAttorney', 'participants.user', 'notes.user', 'documents', 'timeEntries', 'expenses', 'invoices']);
        
        return view('cases.show', compact('case'));
    }

    public function edit(LegalCase $case): View
    {
        $case->load(['status', 'assignedAttorney']);
        $statuses = CaseStatus::all();
        
        return view('cases.edit', compact('case', 'statuses'));
    }

    public function update(Request $request, LegalCase $case): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'case_type' => 'nullable|string|max:100',
            'status_id' => 'required|exists:case_statuses,id',
            'assigned_attorney_id' => 'required|exists:users,id',
            'open_date' => 'required|date',
            'expected_close_date' => 'nullable|date',
            'actual_close_date' => 'nullable|date',
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $case->update($validated);

        return redirect()->route('cases.show', $case)->with('success', 'Case updated successfully.');
    }

    public function destroy(LegalCase $case): RedirectResponse
    {
        $case->delete();
        
        return redirect()->route('cases.index')->with('success', 'Case deleted successfully.');
    }

    public function addNote(Request $request, LegalCase $case): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'is_internal' => 'boolean',
        ]);

        $case->notes()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'is_internal' => $validated['is_internal'] ?? true,
        ]);

        return back()->with('success', 'Note added successfully.');
    }

    public function addParticipant(Request $request, LegalCase $case): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $case->participants()->create($validated);

        return back()->with('success', 'Participant added successfully.');
    }

    public function removeParticipant(LegalCase $case, CaseParticipant $participant): RedirectResponse
    {
        $participant->delete();
        
        return back()->with('success', 'Participant removed successfully.');
    }
}