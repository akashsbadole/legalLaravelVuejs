<?php

namespace App\Http\Controllers;

use App\Events\CaseCreated;
use App\Models\CaseFollowUp;
use App\Models\CaseParticipant;
use App\Models\CaseStatus;
use App\Models\CaseTimelineEvent;
use App\Models\InteractionLog;
use App\Models\LegalCase;
use App\Services\ConflictCheckService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseController extends Controller
{
    protected $conflictCheck;

    public function __construct()
    {
        $this->conflictCheck = new ConflictCheckService;
    }

    public function index(Request $request): View
    {
        $query = LegalCase::with(['status', 'assignedAttorney']);

        // Filter cases based on user role
        $user = auth()->user();
        if ($user->isClient()) {
            // Clients can only see cases they are participants in
            $query->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif (! $user->isAdmin() && ! $user->isAttorney()) {
            // Paralegals and others can see cases they are assigned to or participants in
            $query->where(function ($q) use ($user) {
                $q->where('assigned_attorney_id', $user->id)
                    ->orWhereHas('participants', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
            });
        }
        // Admins and attorneys can see all cases (no additional filter)

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
            'client_id' => 'required|exists:users,id',
            'open_date' => 'required|date',
            'expected_close_date' => 'nullable|date',
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['case_number'] = LegalCase::generateCaseNumber();

        $case = LegalCase::create($validated);

        // Dispatch case created event for automation
        CaseCreated::dispatch($case);

        // Create timeline event for case creation
        $case->timelineEvents()->create([
            'user_id' => auth()->id(),
            'title' => 'Case Created',
            'description' => 'New case opened with number '.$case->case_number,
            'event_type' => 'status_change',
            'event_date' => now(),
            'is_milestone' => true,
        ]);

        $conflicts = $this->conflictCheck->checkClientConflicts($validated['client_id'], $validated['assigned_attorney_id']);

        if (! empty($conflicts)) {
            return redirect()->route('cases.show', $case)->with('warning', 'Case created but conflict check warning: '.$conflicts[0]['message']);
        }

        return redirect()->route('cases.show', $case)->with('success', 'Case created successfully.');
    }

    public function show(LegalCase $case): View
    {
        $this->authorize('view', $case);

        $case->load([
            'status', 'assignedAttorney', 'participants', 'notes', 'documents',
            'timeEntries', 'expenses', 'invoices', 'followUps.creator', 'followUps.assignee',
            'timelineEvents',
        ]);

        $interactions = InteractionLog::where('case_id', $case->id)
            ->with(['creator'])
            ->orderBy('sent_at', 'desc')
            ->paginate(10);

        return view('cases.show', compact('case', 'interactions'));
    }

    public function edit(LegalCase $case): View
    {
        $this->authorize('update', $case);

        $case->load(['status', 'assignedAttorney']);
        $statuses = CaseStatus::all();

        return view('cases.edit', compact('case', 'statuses'));
    }

    public function update(Request $request, LegalCase $case): RedirectResponse
    {
        $this->authorize('update', $case);

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

    public function timeline(LegalCase $case): View
    {
        $case->load(['timelineEvents.user', 'status', 'assignedAttorney']);
        $eventTypes = CaseTimelineEvent::getEventTypes();

        return view('cases.timeline', compact('case', 'eventTypes'));
    }

    public function storeTimelineEvent(Request $request, LegalCase $case): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|string',
            'event_date' => 'required|date',
            'is_milestone' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $case->timelineEvents()->create($validated);

        return back()->with('success', 'Timeline event added successfully.');
    }

    public function destroyTimelineEvent(CaseTimelineEvent $event): RedirectResponse
    {
        $event->delete();

        return back()->with('success', 'Timeline event deleted.');
    }

    public function createFollowUp(Request $request, LegalCase $case): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date|after:now',
            'reminder_at' => 'nullable|date|before:scheduled_at',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['case_id'] = $case->id;
        $validated['created_by'] = auth()->id();

        $followUp = CaseFollowUp::create($validated);

        // Add timeline event
        $case->timelineEvents()->create([
            'user_id' => auth()->id(),
            'title' => 'Follow-up Scheduled: '.$followUp->title,
            'description' => $followUp->description,
            'event_type' => 'follow_up',
            'event_date' => $followUp->scheduled_at,
            'is_milestone' => false,
        ]);

        return back()->with('success', 'Follow-up scheduled successfully.');
    }

    public function updateFollowUp(Request $request, CaseFollowUp $followUp): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,sent,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $followUp->update($validated);

        return back()->with('success', 'Follow-up updated successfully.');
    }

    public function destroyFollowUp(CaseFollowUp $followUp): RedirectResponse
    {
        $followUp->delete();

        return back()->with('success', 'Follow-up deleted successfully.');
    }

    public function logCall(Request $request, LegalCase $case): RedirectResponse
    {
        $validated = $request->validate([
            'direction' => 'required|in:inbound,outbound',
            'phone_number' => 'required|string',
            'call_duration' => 'nullable|integer|min:0',
            'call_outcome' => 'nullable|in:answered,voicemail,no_answer,busy,completed',
            'subject' => 'nullable|string|max:255',
            'call_notes' => 'nullable|string',
        ]);

        // Determine the user based on direction - for outbound calls it's the creator, for inbound it might be the client
        $userId = $validated['direction'] === 'outbound' ? auth()->id() : $case->client_id;

        $interaction = InteractionLog::create([
            'user_id' => $userId,
            'case_id' => $case->id,
            'created_by' => auth()->id(),
            'type' => 'call',
            'subject' => $validated['subject'],
            'phone_number' => $validated['phone_number'],
            'call_duration' => $validated['call_duration'],
            'call_outcome' => $validated['call_outcome'],
            'call_notes' => $validated['call_notes'],
            'sent_at' => now(),
            'direction' => $validated['direction'],
            'status' => 'completed',
        ]);

        // Add timeline event
        $case->timelineEvents()->create([
            'user_id' => auth()->id(),
            'title' => 'Call '.ucfirst($validated['direction']).': '.($validated['subject'] ?: 'No subject'),
            'description' => 'Call logged - '.($validated['call_outcome'] ?: 'Outcome not specified'),
            'event_type' => 'client_communication',
            'event_date' => now(),
            'is_milestone' => false,
        ]);

        return back()->with('success', 'Call logged successfully.');
    }

    public function assignStaff(Request $request, LegalCase $case): RedirectResponse
    {
        $this->authorize('update', $case);

        $validated = $request->validate([
            'assigned_attorney_id' => 'nullable|exists:users,id',
            'assigned_staff' => 'nullable|array',
            'assigned_staff.*' => 'exists:users,id',
        ]);

        $oldAssignment = [
            'attorney' => $case->assigned_attorney_id,
            'staff' => $case->assigned_staff ?? [],
        ];

        $case->update([
            'assigned_attorney_id' => $validated['assigned_attorney_id'],
            'assigned_staff' => $validated['assigned_staff'] ?? [],
            'assignment_changed_at' => now(),
            'assignment_changed_by' => auth()->id(),
        ]);

        // Add timeline event
        $case->timelineEvents()->create([
            'user_id' => auth()->id(),
            'title' => 'Staff Assignment Changed',
            'description' => 'Case staff assignment was updated',
            'event_type' => 'status_change',
            'event_date' => now(),
            'is_milestone' => false,
        ]);

        return back()->with('success', 'Staff assignment updated successfully.');
    }
}
