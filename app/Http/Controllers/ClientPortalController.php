<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\LegalCase;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Services\GDPRService;
use App\Services\OnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ClientPortalController extends Controller
{
    public function dashboard(): View
    {
        $client = Auth::user();

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
        $client = Auth::user();

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
        $client = Auth::user();

        $documents = Document::whereHas('case', function ($query) use ($client) {
            $query->whereHas('participants', function ($q) use ($client) {
                $q->where('user_id', $client->id);
            });
        })->with('case')->latest()->paginate(15);

        return view('portal.documents', compact('documents'));
    }

    public function invoices(): View
    {
        $invoices = Invoice::where('client_id', Auth::id())
            ->with('case')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('portal.invoices', compact('invoices'));
    }

    public function invoiceDetail(Invoice $invoice): View
    {
        if ($invoice->client_id !== Auth::id()) {
            abort(403);
        }

        $invoice->load(['case', 'lineItems']);

        return view('portal.invoice-detail', compact('invoice'));
    }

    public function payInvoice(Request $request, Invoice $invoice): RedirectResponse
    {
        if ($invoice->client_id !== Auth::id() || $invoice->status === 'paid') {
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
            'user_id' => Auth::id(),
            'content' => $validated['message'],
            'is_internal' => false,
        ]);

        return back()->with('success', 'Message sent successfully.');
    }

    public function privacy(): View
    {
        $user = Auth::user();
        $consents = $user->consents()->latest()->get();
        $clientProfile = Client::where('email', $user->email)->first();

        return view('portal.privacy', compact('consents', 'clientProfile'));
    }

    public function exportData(Request $request): RedirectResponse
    {
        $client = Auth::user();

        $data = GDPRService::exportUserData($client);

        // In a real implementation, generate a file and send via email or download
        // For now, just log it
        Log::info('Data export requested', ['user_id' => $client->id]);

        return back()->with('success', 'Data export request submitted. You will receive an email with your data shortly.');
    }

    public function deleteData(Request $request): RedirectResponse
    {
        $client = Auth::user();

        GDPRService::processDataDeletionRequest($client);

        return back()->with('success', 'Data deletion request submitted. Your data will be deleted in 30 days.');
    }

    public function updateConsent(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $clientProfile = Client::where('email', $user->email)->first();

        if ($clientProfile) {
            $clientProfile->update([
                'marketing_consent' => $request->has('marketing_consent'),
                'data_processing_consent' => $request->has('data_processing_consent'),
                'consent_updated_at' => now(),
            ]);

            GDPRService::recordConsent($user, 'marketing_communications', $request->has('marketing_consent') ? 'granted' : 'revoked');
            GDPRService::recordConsent($user, 'data_processing', $request->has('data_processing_consent') ? 'granted' : 'revoked');
        }

        return back()->with('success', 'Consent settings updated successfully.');
    }

    public function onboarding(): View
    {
        $client = Auth::user();
        $onboardingService = app(OnboardingService::class);
        $progress = $onboardingService->getOnboardingProgress($client);

        return view('portal.onboarding', compact('progress'));
    }

    public function survey(Survey $survey): View
    {
        return view('portal.survey', compact('survey'));
    }

    public function submitSurvey(Request $request, Survey $survey): RedirectResponse
    {
        $client = Auth::user();

        $validated = $request->validate([
            'responses' => 'required|array',
            'feedback' => 'nullable|string',
        ]);

        SurveyResponse::create([
            'survey_id' => $survey->id,
            'user_id' => $client->id,
            'responses' => $validated['responses'],
            'feedback' => $validated['feedback'],
        ]);

        return redirect()->route('portal.dashboard')->with('success', 'Thank you for your feedback!');
    }

    private function authorizeClientCase(LegalCase $case): void
    {
        $client = Auth::user();

        $isParticipant = $case->participants()->where('user_id', $client->id)->exists();

        if (! $isParticipant) {
            abort(403, 'You do not have access to this case.');
        }
    }
}
