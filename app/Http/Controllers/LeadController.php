<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lead;
use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

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
            'user_id' => Auth::id(),
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
                'user_id' => Auth::id(),
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

        $validated['user_id'] = Auth::id();
        $lead->activities()->create($validated);

        return back()->with('success', 'Activity added successfully.');
    }

    public function convertToCase(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'case_title' => 'required|string|max:255',
            'create_client' => 'boolean',
        ]);

        // Create client user if email provided
        $clientUser = null;
        if ($validated['create_client'] && $lead->email) {
            $clientUser = User::firstOrCreate(
                ['email' => $lead->email],
                [
                    'name' => $lead->name,
                    'password' => bcrypt(Str::random(12)), // Temporary password
                    'role' => 'client',
                ]
            );

            // Create client profile
            Client::create([
                'first_name' => explode(' ', $lead->name)[0] ?? $lead->name,
                'last_name' => explode(' ', $lead->name)[1] ?? '',
                'email' => $lead->email,
                'phone' => $lead->phone,
                'client_type' => $lead->company ? 'corporate' : 'individual',
                'company_name' => $lead->company,
                'status' => 'active',
                'address_line1' => 'To be updated',
                'city' => 'To be updated',
                'state' => 'To be updated',
                'postal_code' => '000000',
            ]);
        }

        $case = LegalCase::create([
            'case_number' => LegalCase::generateCaseNumber(),
            'title' => $validated['case_title'],
            'description' => $lead->description,
            'status_id' => 1,
            'assigned_attorney_id' => $lead->assigned_to,
            'client_id' => $clientUser?->id,
            'open_date' => now()->toDateString(),
        ]);

        if ($clientUser) {
            $case->participants()->attach($clientUser->id, ['role' => 'client']);
        }

        $lead->update(['status' => 'won']);

        $lead->activities()->create([
            'user_id' => Auth::id(),
            'activity_type' => 'converted',
            'notes' => "Converted to case: {$case->case_number}".($clientUser ? ' and client profile created' : ''),
        ]);

        return redirect()->route('cases.show', $case)->with('success', 'Lead converted to case successfully.');
    }
}
