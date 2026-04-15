<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'category' => 'required|string',
            'priority' => 'required|in:normal,urgent,emergency',
            'message' => 'required|string|max:2000',
        ]);

        // Create enquiry
        $enquiry = Enquiry::create($validated);

        // Send notification email to admin
        $this->notifyAdmin($enquiry);

        // Send confirmation email to user
        $this->sendConfirmationEmail($enquiry);

        return back()->with('success', 'Thank you for your enquiry! We\'ll get back to you within 24 hours.');
    }

    public function index(Request $request): View
    {
        $query = Enquiry::with(['assignedUser', 'assignedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        $enquiries = $query->orderBy('created_at', 'desc')->paginate(15);

        $lawyers = User::whereIn('role', ['admin', 'attorney'])->get();

        return view('admin.enquiries.index', compact('enquiries', 'lawyers'));
    }

    public function show(Enquiry $enquiry): View
    {
        $enquiry->load(['assignedUser', 'assignedBy']);

        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function assign(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $enquiry->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'status' => 'assigned',
            'admin_notes' => $validated['admin_notes'],
        ]);

        // Notify assigned lawyer
        $this->notifyAssignedLawyer($enquiry);

        return back()->with('success', 'Enquiry assigned successfully.');
    }

    public function updateStatus(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:new,assigned,in_progress,resolved,closed',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
        ];

        if ($validated['status'] === 'resolved' || $validated['status'] === 'closed') {
            $updateData['resolved_at'] = now();
        }

        $enquiry->update($updateData);

        // Notify user if resolved
        if (in_array($validated['status'], ['resolved', 'closed'])) {
            $this->notifyUserResolution($enquiry);
        }

        return back()->with('success', 'Enquiry status updated successfully.');
    }

    private function notifyAdmin(Enquiry $enquiry): void
    {
        // Send email to all admins
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $this->emailService->send([
                'email' => $admin->email,
                'name' => $admin->name,
            ], 'New Enquiry Received', "A new enquiry has been submitted by {$enquiry->name} regarding {$enquiry->getCategoryDisplay()}.", [
                'Please review and assign this enquiry to an appropriate lawyer.',
            ]);
        }
    }

    private function sendConfirmationEmail(Enquiry $enquiry): void
    {
        $this->emailService->send([
            'email' => $enquiry->email,
            'name' => $enquiry->name,
        ], 'Enquiry Received - LegalFirm', 'Thank you for contacting LegalFirm. We have received your enquiry and will respond within 24 hours.', [
            'Enquiry Details:',
            "Category: {$enquiry->getCategoryDisplay()}",
            "Priority: {$enquiry->priority}",
            "Message: {$enquiry->message}",
        ]);
    }

    private function notifyAssignedLawyer(Enquiry $enquiry): void
    {
        $lawyer = $enquiry->assignedUser;

        if ($lawyer) {
            $this->emailService->send([
                'email' => $lawyer->email,
                'name' => $lawyer->name,
            ], 'New Enquiry Assigned', "You have been assigned a new enquiry from {$enquiry->name}.", [
                "Category: {$enquiry->getCategoryDisplay()}",
                "Priority: {$enquiry->priority}",
                "Message: {$enquiry->message}",
                'Please review and respond to this enquiry.',
            ]);
        }
    }

    private function notifyUserResolution(Enquiry $enquiry): void
    {
        $this->emailService->send([
            'email' => $enquiry->email,
            'name' => $enquiry->name,
        ], 'Enquiry Resolved - LegalFirm', 'Your enquiry has been resolved. Thank you for contacting us.', [
            'If you need further assistance, please don\'t hesitate to contact us again.',
        ]);
    }
}
