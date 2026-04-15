<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $admin;
    protected User $attorney;
    protected User $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->admin = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role' => 'admin',
        ]);
        $this->attorney = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role' => 'attorney',
        ]);
        $this->client = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role' => 'client',
        ]);
    }

    public function test_all_roles_can_access_settings(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/settings');

        $response->assertStatus(200);

        $response = $this->actingAs($this->attorney, 'sanctum')
            ->getJson('/api/settings');

        $response->assertStatus(200);

        $response = $this->actingAs($this->client, 'sanctum')
            ->getJson('/api/settings');

        $response->assertStatus(200);
    }

    public function test_user_can_view_own_profile(): void
    {
        $response = $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/tenant/{$this->tenant->id}/user", [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(200);
    }

    public function test_authenticated_users_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/dashboard/stats");

        $response->assertStatus(200);

        $response = $this->actingAs($this->attorney, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/dashboard/stats");

        $response->assertStatus(200);

        $response = $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/dashboard/stats");

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $response = $this->getJson("/api/tenant/{$this->tenant->id}/dashboard/stats");

        $response->assertStatus(401);

        $response = $this->getJson('/api/settings');

        $response->assertStatus(401);
    }
}