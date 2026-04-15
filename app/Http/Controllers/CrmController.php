<?php

namespace App\Http\Controllers;

use App\Jobs\SendFollowUp;
use App\Models\Document;
use App\Models\FollowUp;
use App\Models\InteractionLog;
use App\Models\Invoice;
use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmController extends Controller
{
    public function dashboard(): View
    {
        $clients = User::where('role', 'client')->with('interactionLogs')->get();
        $recentInteractions = InteractionLog::with('user')->latest()->take(10)->get();

        return view('crm.dashboard', compact('clients', 'recentInteractions'));
    }

    public function logInteraction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:call,email,sms,meeting,message',
            'subject' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'recipients' => 'nullable|array',
            'sent_at' => 'nullable|date',
            'direction' => 'required|in:inbound,outbound',
            'status' => 'required|in:sent,delivered,failed',
            'notes' => 'nullable|string',
        ]);

        InteractionLog::create([
            'user_id' => $validated['user_id'],
            'created_by' => auth()->id(),
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'recipients' => $validated['recipients'],
            'sent_at' => $validated['sent_at'] ?: now(),
            'direction' => $validated['direction'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Interaction logged successfully.');
    }

    public function clientInteractions(User $client): View
    {
        $interactions = $client->interactionLogs()->with('creator')->latest()->paginate(20);
        $followUps = $client->followUps()->with('creator')->latest()->paginate(20);
        $cases = LegalCase::where('client_id', $client->id)->with('status', 'assignedAttorney')->get();
        $documents = Document::where('case_id', '!=', null)->whereHas('case', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        })->latest()->take(5)->get();
        $invoices = Invoice::where('client_id', $client->id)->latest()->take(5)->get();
        $totalInvoiced = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('amount_paid');
        $outstanding = $totalInvoiced - $totalPaid;

        return view('crm.client-interactions', compact('client', 'interactions', 'followUps', 'cases', 'documents', 'invoices', 'totalInvoiced', 'totalPaid', 'outstanding'));
    }

    public function createInteraction(User $client): View
    {
        return view('crm.create-interaction', compact('client'));
    }

    public function createFollowUpForm(User $client): View
    {
        return view('crm.create-follow-up', compact('client'));
    }

    public function createFollowUp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:email,sms,call',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'scheduled_at' => 'required|date|after:now',
        ]);

        $followUp = FollowUp::create([
            'user_id' => $validated['user_id'],
            'created_by' => auth()->id(),
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'scheduled_at' => $validated['scheduled_at'],
        ]);

        // Schedule the job
        SendFollowUp::dispatch($followUp)->delay($followUp->scheduled_at);

        return back()->with('success', 'Follow-up scheduled successfully.');
    }
}
