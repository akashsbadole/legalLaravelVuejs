<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiInvoicesTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $admin;
    protected User $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->admin = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role' => 'admin',
        ]);
        $this->client = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role' => 'client',
        ]);
    }

    public function test_user_can_list_invoices(): void
    {
        Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'client_id' => $this->client->id,
        ]);
        Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/invoices");

        $response->assertStatus(200);
    }

    public function test_user_can_create_invoice(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/tenant/{$this->tenant->id}/invoices", [
                'client_id' => $this->client->id,
                'total' => 1000.00,
                'due_date' => now()->addDays(30)->toDateString(),
                'description' => 'Legal services rendered',
                'status' => 'pending',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('invoices', [
            'client_id' => $this->client->id,
            'total_amount' => 1000.00,
        ]);
    }

    public function test_invoice_creation_requires_client_and_total(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/tenant/{$this->tenant->id}/invoices", [
                'due_date' => now()->addDays(30)->toDateString(),
            ]);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_user_cannot_access_invoices(): void
    {
        $response = $this->getJson("/api/tenant/{$this->tenant->id}/invoices");

        $response->assertStatus(401);
    }
}