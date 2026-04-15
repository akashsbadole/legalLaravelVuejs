<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTrustAccountsTest extends TestCase
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

    public function test_user_can_list_trust_accounts(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/trust-accounts");

        $response->assertStatus(200);
    }

    public function test_user_can_get_trust_account_stats(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/trust-accounts/stats");

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_trust_accounts(): void
    {
        $response = $this->getJson("/api/tenant/{$this->tenant->id}/trust-accounts");

        $response->assertStatus(401);
    }
}