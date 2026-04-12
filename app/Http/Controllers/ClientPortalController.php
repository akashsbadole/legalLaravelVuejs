<?php

namespace App\Http\Controllers;

use App\Models\LegalCase;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\CaseNote;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ClientPortalController extends Controller
{
    public function dashboard(): View
    {
        $client = auth()->user();
        
        $cases = LegalCase::whereHas('participants', function ($query) use ($client) {
            $query->where('user_id', $client->id);
        })->with('status', 'assignedAttorney')->get();

        $documents = Document::whereHas('case', function ($query) use ($client) {
            $query->whereHas('participants', function ($q) use ($client) {
                $q->where('user_id', $client->id);
            });
        })->with('case')->latest()->take(10)->get();

        $invoices = Invoice::where('client_id', $client->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('portal.dashboard', compact('cases', 'documents', 'invoices'));
    }

    public function cases(): View
    {
        $client = auth()->user();
        
        $cases = LegalCase::whereHas('participants', function ($query) use ($client) {
            $query->where('user_id', $client->id);
        })->with('status', 'assignedAttorney', 'documents', 'notes')->paginate(10);

        return view('portal.cases', compact('cases'));
    }

    public function caseDetail(LegalCase $case): View
    {
        $this->authorizeClientCase($case);
        
        $case->load(['status', 'assignedAttorney', 'participants.user', 'documents', 'notes.user']);
        
        return view('portal.case-detail', compact('case'));
    }

    public function documents(): View
    {
        $client = auth()->user();
        
        $documents = Document::whereHas('case', function ($query) use ($client) {
            $query->whereHas('participants', function ($q) use ($client) {
                $q->where('user_id', $client->id);
            });
        })->with('case')->latest()->paginate(15);

        return view('portal.documents', compact('documents'));
    }

    public function invoices(): View
    {
        $invoices = Invoice::where('client_id', auth()->id())
            ->with('case')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('portal.invoices', compact('invoices'));
    }

    public function invoiceDetail(Invoice $invoice): View
    {
        if ($invoice->client_id !== auth()->id()) {
            abort(403);
        }

        $invoice->load(['case', 'lineItems']);
        return view('portal.invoice-detail', compact('invoice'));
    }

    public function payInvoice(Request $request, Invoice $invoice): RedirectResponse
    {
        if ($invoice->client_id !== auth()->id() || $invoice->status === 'paid') {
            abort(403);
        }

        $invoice->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);

        return back()->with('success', 'Payment successful! Thank you for your payment.');
    }

    public function sendMessage(Request $request, LegalCase $case): RedirectResponse
    {
        $this->authorizeClientCase($case);

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $case->notes()->create([
            'user_id' => auth()->id(),
            'content' => $validated['message'],
            'is_internal' => false,
        ]);

        return back()->with('success', 'Message sent successfully.');
    }

    private function authorizeClientCase(LegalCase $case): void
    {
        $client = auth()->user();
        
        $isParticipant = $case->participants()->where('user_id', $client->id)->exists();
        
        if (!$isParticipant) {
            abort(403, 'You do not have access to this case.');
        }
    }
}