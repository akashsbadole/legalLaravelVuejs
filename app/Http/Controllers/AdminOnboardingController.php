<?php

namespace App\Http\Controllers;

use App\Models\AdminOnboarding;
use App\Models\User;
use App\Models\LegalCase;
use App\Models\CaseStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminOnboardingController extends Controller
{
    public function index(): View
    {
        $admin = auth()->user();

        $onboarding = AdminOnboarding::firstOrCreate(
            ['admin_id' => $admin->id],
            ['current_step' => 1, 'completed_steps' => []]
        );

        $stepData = $onboarding->getCurrentStepData();

        // Get data needed for different steps
        $context = $this->getStepContext($onboarding->current_step);

        return view('admin.onboarding.index', compact('onboarding', 'stepData', 'context'));
    }

    public function next(Request $request): RedirectResponse
    {
        $admin = auth()->user();
        $onboarding = AdminOnboarding::where('admin_id', $admin->id)->first();

        if (!$onboarding) {
            return redirect()->route('dashboard');
        }

        $currentStep = $onboarding->current_step;

        // Process step-specific actions
        $this->processStep($request, $currentStep);

        // Complete current step and move to next
        $onboarding->completeStep($currentStep);

        if ($onboarding->is_completed) {
            return redirect()->route('dashboard')->with('success', 'Welcome! Your system is ready to use.');
        }

        return redirect()->route('admin.onboarding');
    }

    public function skip(): RedirectResponse
    {
        $admin = auth()->user();
        $onboarding = AdminOnboarding::where('admin_id', $admin->id)->first();

        if ($onboarding) {
            $onboarding->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }

        return redirect()->route('dashboard');
    }

    private function getStepContext(int $step): array
    {
        $context = [];

        switch ($step) {
            case 2: // Create First Attorney
                $context['attorneys_count'] = User::where('role', 'attorney')->count();
                break;

            case 3: // Set Up Case Types
                $context['case_types'] = LegalCase::distinct('case_type')->pluck('case_type')->filter()->values();
                $context['case_statuses'] = CaseStatus::all();
                break;

            case 4: // Configure Billing
                $context['attorneys'] = User::where('role', 'attorney')->get();
                break;

            case 5: // Explore Features
                $context['features'] = [
                    'case_management' => LegalCase::count(),
                    'clients' => User::where('role', 'client')->count(),
                    'tasks' => \App\Models\CaseTask::count(),
                    'invoices' => \App\Models\Invoice::count(),
                ];
                break;
        }

        return $context;
    }

    private function processStep(Request $request, int $step): void
    {
        switch ($step) {
            case 2: // Create Attorney
                if ($request->has('create_attorney')) {
                    $request->validate([
                        'name' => 'required|string|max:255',
                        'email' => 'required|email|unique:users',
                        'phone' => 'nullable|string|max:20',
                    ]);

                    User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'password' => bcrypt('password123'), // Temporary password
                        'role' => 'attorney',
                    ]);
                }
                break;

            case 3: // Case Types - handled in view
                break;

            case 4: // Billing setup
                if ($request->has('update_rates')) {
                    $rates = $request->validate([
                        'hourly_rates' => 'required|array',
                        'hourly_rates.*' => 'numeric|min:0',
                    ]);

                    foreach ($rates['hourly_rates'] as $userId => $rate) {
                        User::where('id', $userId)->update(['hourly_rate' => $rate]);
                    }
                }
                break;

            case 5: // Feature tour - handled in view
                break;
        }
    }
}
