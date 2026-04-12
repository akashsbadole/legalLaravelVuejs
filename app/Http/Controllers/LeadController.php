<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $query = Lead::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('company', 'like', "%{$request->search}%");
            });
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('leads.index', compact('leads'));
    }

    public function create(): View
    {
        return view('leads.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'source' => 'nullable|in:website,referral,cold_call,advertisement,other',
            'priority' => 'nullable|in:low,medium,high',
            'estimated_value' => 'nullable|numeric|min:0',
            'next_follow_up' => 'nullable|date',
        ]);

        $validated['status'] = 'new';
        
        $lead = Lead::create($validated);

        $lead->activities()->create([
            'user_id' => auth()->id(),
            'activity_type' => 'created',
            'notes' => 'Lead created',
        ]);

        return redirect()->route('leads.show', $lead)->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['assignedTo', 'activities.user']);

        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead): View
    {
        return view('leads.edit', compact('lead'));
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'source' => 'nullable|in:website,referral,cold_call,advertisement,other',
            'status' => 'nullable|in:new,contacted,qualified,proposal,won,lost',
            'priority' => 'nullable|in:low,medium,high',
            'estimated_value' => 'nullable|numeric|min:0',
            'next_follow_up' => 'nullable|date',
        ]);

        $oldStatus = $lead->status;
        $lead->update($validated);

        if ($oldStatus !== $validated['status']) {
            $lead->activities()->create([
                'user_id' => auth()->id(),
                'activity_type' => 'status_changed',
                'notes' => "Status changed from {$oldStatus} to {$validated['status']}",
            ]);
        }

        return redirect()->route('leads.show', $lead)->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function addActivity(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'activity_type' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $lead->activities()->create($validated);

        return back()->with('success', 'Activity added successfully.');
    }

    public function convertToCase(Request $request, Lead $lead): RedirectResponse
    {
        $request->validate([
            'case_title' => 'required|string|max:255',
        ]);

        $case = \App\Models\LegalCase::create([
            'case_number' => \App\Models\LegalCase::generateCaseNumber(),
            'title' => $request->case_title,
            'description' => $lead->description,
            'status_id' => 1,
            'assigned_attorney_id' => $lead->assigned_to,
            'open_date' => now()->toDateString(),
        ]);

        $case->participants()->attach($lead->assigned_to, ['role' => 'client']);

        $lead->update(['status' => 'won']);
        
        $lead->activities()->create([
            'user_id' => auth()->id(),
            'activity_type' => 'converted',
            'notes' => "Converted to case: {$case->case_number}",
        ]);

        return redirect()->route('cases.show', $case)->with('success', 'Lead converted to case successfully.');
    }
}