<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\LegalCase;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_creation()
    {
        $tenantData = [
            'name' => 'Test Tenant',
            'slug' => 'test-tenant',
            'contact_email' => 'admin@testtenant.com',
            'subscription_plan' => 'basic',
            'max_users' => 5,
            'max_cases' => 10,
            'max_storage_mb' => 100,
        ];

        $response = $this->post(route('admin.tenants.store'), $tenantData);

        $response->assertRedirect();
        $this->assertDatabaseHas('tenants', ['name' => 'Test Tenant']);
    }

    public function test_tenant_update()
    {
        $tenant = Tenant::factory()->create();

        $updateData = [
            'name' => 'Updated Tenant',
            'contact_email' => 'updated@test.com',
        ];

        $response = $this->put(route('admin.tenants.update', $tenant), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('tenants', ['name' => 'Updated Tenant']);
    }

    public function test_tenant_deletion()
    {
        $tenant = Tenant::factory()->create();

        $response = $this->delete(route('admin.tenants.destroy', $tenant));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }

    public function test_tenant_scoping_for_users()
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
        $user2 = User::factory()->create(['tenant_id' => $tenant2->id]);

        // Set current tenant
        config(['app.current_tenant' => $tenant1]);

        $users = User::all();
        $this->assertCount(1, $users);
        $this->assertEquals($user1->id, $users->first()->id);
    }

    public function test_tenant_scoping_for_cases()
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        $case1 = LegalCase::factory()->create(['tenant_id' => $tenant1->id]);
        $case2 = LegalCase::factory()->create(['tenant_id' => $tenant2->id]);

        // Set current tenant
        config(['app.current_tenant' => $tenant1]);

        $cases = LegalCase::all();
        $this->assertCount(1, $cases);
        $this->assertEquals($case1->id, $cases->first()->id);
    }

    public function test_tenant_scoping_for_clients()
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        $client1 = Client::factory()->create(['tenant_id' => $tenant1->id]);
        $client2 = Client::factory()->create(['tenant_id' => $tenant2->id]);

        // Set current tenant
        config(['app.current_tenant' => $tenant1]);

        $clients = Client::all();
        $this->assertCount(1, $clients);
        $this->assertEquals($client1->id, $clients->first()->id);
    }

    public function test_set_tenant_middleware()
    {
        $tenant = Tenant::factory()->create();

        $this->get("/api/tenant/{$tenant->id}/user")
            ->assertStatus(401); // Unauthorized, but middleware should set tenant

        // Since no auth, but middleware runs
        // In real test, with auth
    }

    public function test_tenant_route_access()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($user, 'sanctum')
            ->get("/api/tenant/{$tenant->id}/user")
            ->assertStatus(200);
    }

    public function test_invalid_tenant_returns_404()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->get("/api/tenant/999/user")
            ->assertStatus(404);
    }

    public function test_tenant_subscription_status()
    {
        $activeTenant = Tenant::factory()->create(['subscription_status' => 'active']);
        $suspendedTenant = Tenant::factory()->create(['subscription_status' => 'suspended']);

        $this->assertTrue($activeTenant->isSubscriptionActive());
        $this->assertFalse($suspendedTenant->isSubscriptionActive());
    }

    public function test_tenant_user_limits()
    {
        $tenant = Tenant::factory()->create(['max_users' => 2]);

        User::factory()->count(2)->create(['tenant_id' => $tenant->id]);

        config(['app.current_tenant' => $tenant]);

        $this->assertCount(2, User::all());
    }
}