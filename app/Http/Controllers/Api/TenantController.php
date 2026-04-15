<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TenantController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenants = Tenant::latest()->paginate(20);
        
        return response()->json([
            'tenants' => $tenants->items(),
            'total' => $tenants->total(),
            'current_page' => $tenants->currentPage(),
            'last_page' => $tenants->lastPage()
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tenants',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'subscription_plan' => 'nullable|in:basic,professional,enterprise',
            'subscription_status' => 'nullable|in:active,trial,suspended,cancelled',
            'max_users' => 'nullable|integer',
            'max_cases' => 'nullable|integer',
        ]);

        $tenant = Tenant::create($validated);

        return response()->json([
            'tenant' => $tenant,
            'message' => 'Tenant created successfully'
        ], 201);
    }

    public function show(Tenant $tenant): JsonResponse
    {
        $tenant->load('users');
        
        return response()->json([
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'logo' => $tenant->logo,
                'primary_color' => $tenant->primary_color,
                'contact_email' => $tenant->contact_email,
                'contact_phone' => $tenant->contact_phone,
                'address' => $tenant->address,
                'subscription_plan' => $tenant->subscription_plan,
                'subscription_status' => $tenant->subscription_status,
                'subscription_expires_at' => $tenant->subscription_expires_at?->toDateString(),
                'max_users' => $tenant->max_users,
                'max_cases' => $tenant->max_cases,
                'is_active' => $tenant->is_active,
                'users_count' => $tenant->users->count(),
                'created_at' => $tenant->created_at->toDateString()
            ]
        ]);
    }

    public function update(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:tenants,slug,' . $tenant->id,
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'subscription_plan' => 'nullable|in:basic,professional,enterprise',
            'subscription_status' => 'nullable|in:active,trial,suspended,cancelled',
            'subscription_expires_at' => 'nullable|date',
            'max_users' => 'nullable|integer',
            'max_cases' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $tenant->update($validated);

        return response()->json([
            'tenant' => $tenant,
            'message' => 'Tenant updated successfully'
        ]);
    }

    public function destroy(Tenant $tenant): JsonResponse
    {
        $tenant->delete();

        return response()->json([
            'message' => 'Tenant deleted successfully'
        ]);
    }
}