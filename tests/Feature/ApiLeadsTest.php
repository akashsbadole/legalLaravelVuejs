<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiLeadsTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->admin = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role' => 'admin',
        ]);
    }

    public function test_user_can_list_leads(): void
    {
        Lead::create(['tenant_id' => $this->tenant->id, 'name' => 'Lead 1', 'email' => 'lead1@test.com']);
        Lead::create(['tenant_id' => $this->tenant->id, 'name' => 'Lead 2', 'email' => 'lead2@test.com']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/leads");

        $response->assertStatus(200);
    }

    public function test_user_can_create_lead(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/tenant/{$this->tenant->id}/leads", [
                'name' => 'New Lead',
                'email' => 'lead@example.com',
                'phone' => '+1234567890',
                'company' => 'Acme Corp',
                'source' => 'website',
                'status' => 'new',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('leads', [
            'name' => 'New Lead',
            'email' => 'lead@example.com',
        ]);
    }

    public function test_lead_creation_requires_name(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/tenant/{$this->tenant->id}/leads", [
                'email' => 'lead@example.com',
            ]);

        $response->assertStatus(422);
    }

    public function test_lead_email_is_optional(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/tenant/{$this->tenant->id}/leads", [
                'name' => 'New Lead',
                'phone' => '+1234567890',
                'company' => 'Acme Corp',
            ]);

        $response->assertStatus(201);
    }

    public function test_unauthenticated_user_cannot_access_leads(): void
    {
        $response = $this->getJson("/api/tenant/{$this->tenant->id}/leads");

        $response->assertStatus(401);
    }
}