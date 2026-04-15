<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiClientsTest extends TestCase
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

    public function test_user_can_list_clients(): void
    {
        User::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
            'role' => 'client',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/clients");

        $response->assertStatus(200);
    }

    public function test_user_can_list_all_clients_without_pagination(): void
    {
        User::factory()->count(5)->create([
            'tenant_id' => $this->tenant->id,
            'role' => 'client',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/clients/all");

        $response->assertStatus(200);
    }

    public function test_user_can_create_client(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/tenant/{$this->tenant->id}/clients", [
                'name' => 'New Client',
                'email' => 'newclient@example.com',
                'phone' => '+1234567890',
                'address' => '123 Main St',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => 'New Client',
            'email' => 'newclient@example.com',
            'role' => 'client',
        ]);
    }

    public function test_client_creation_requires_name_and_email(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/tenant/{$this->tenant->id}/clients", [
                'phone' => '+1234567890',
            ]);

        $response->assertStatus(422);
    }

    public function test_client_creation_requires_unique_email(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
            'tenant_id' => $this->tenant->id,
            'role' => 'client',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/tenant/{$this->tenant->id}/clients", [
                'name' => 'New Client',
                'email' => 'existing@example.com',
            ]);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_user_cannot_access_clients(): void
    {
        $response = $this->getJson("/api/tenant/{$this->tenant->id}/clients");

        $response->assertStatus(401);
    }
}