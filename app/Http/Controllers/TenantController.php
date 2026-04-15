<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tenant::query();

        if ($request->filled('status')) {
            $query->where('subscription_status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->where('subscription_plan', $request->plan);
        }

        $tenants = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        $plans = Tenant::getPlans();

        return view('admin.tenants.create', compact('plans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:tenants,slug',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'GSTIN' => 'nullable|string',
            'PAN' => 'nullable|string',
            'subscription_plan' => 'required|in:basic,professional,enterprise',
            'max_users' => 'required|integer|min:1',
            'max_cases' => 'required|integer|min:1',
            'max_storage_mb' => 'required|integer|min:100',
        ]);

        Tenant::create($validated);

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant created successfully.');
    }

    public function show(Tenant $tenant): View
    {
        $tenant->load(['users', 'cases']);

        $stats = [
            'total_users' => $tenant->users()->count(),
            'total_cases' => $tenant->cases()->count(),
            'active_cases' => $tenant->cases()->whereHas('status', fn ($q) => $q->where('name', '!=', 'Closed'))->count(),
            'storage_used_mb' => 0,
        ];

        return view('admin.tenants.show', compact('tenant', 'stats'));
    }

    public function edit(Tenant $tenant): View
    {
        $plans = Tenant::getPlans();

        return view('admin.tenants.edit', compact('tenant', 'plans'));
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:tenants,slug,'.$tenant->id,
            'contact_email' => 'sometimes|email',
            'subscription_plan' => 'sometimes|in:basic,professional,enterprise',
            'subscription_status' => 'sometimes|in:active,suspended,expired,cancelled',
        ]);

        $tenant->update($validated);

        return redirect()->route('admin.tenants.show', $tenant)->with('success', 'Tenant updated successfully.');
    }

    public function suspend(Tenant $tenant): RedirectResponse
    {
        $tenant->update(['subscription_status' => 'suspended', 'is_active' => false]);

        return back()->with('info', 'Tenant suspended successfully.');
    }

    public function activate(Tenant $tenant): RedirectResponse
    {
        $tenant->update(['subscription_status' => 'active', 'is_active' => true]);

        return back()->with('success', 'Tenant activated successfully.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        if ($tenant->users()->count() > 0) {
            return back()->with('error', 'Cannot delete tenant with existing users.');
        }

        $tenant->delete();

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant deleted successfully.');
    }

    public function extendSubscription(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $currentExpiry = $tenant->subscription_expires_at ?? now();
        $newExpiry = $currentExpiry->addDays($validated['days']);

        $tenant->update([
            'subscription_expires_at' => $newExpiry,
            'subscription_status' => 'active',
            'is_active' => true,
        ]);

        return back()->with('success', 'Subscription extended by '.$validated['days'].' days.');
    }
}
