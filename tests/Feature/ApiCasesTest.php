<?php

namespace Tests\Feature;

use App\Models\CaseStatus;
use App\Models\LegalCase;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCasesTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected int $openStatusId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role' => 'attorney',
        ]);
        
        $openStatus = CaseStatus::create(['name' => 'Open', 'color' => '#22c55e']);
        CaseStatus::create(['name' => 'Closed', 'color' => '#6b7280']);
        CaseStatus::create(['name' => 'Pending', 'color' => '#f59e0b']);
        
        $this->openStatusId = $openStatus->id;
    }

    public function test_user_can_list_cases(): void
    {
        LegalCase::factory()->create(['tenant_id' => $this->tenant->id, 'status_id' => $this->openStatusId]);
        LegalCase::factory()->create(['tenant_id' => $this->tenant->id, 'status_id' => $this->openStatusId]);
        LegalCase::factory()->create(['tenant_id' => $this->tenant->id, 'status_id' => $this->openStatusId]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/cases");

        $response->assertStatus(200);
    }

    public function test_user_can_search_cases_by_status(): void
    {
        $closedStatus = CaseStatus::where('name', 'Closed')->first();

        LegalCase::factory()->create([
            'tenant_id' => $this->tenant->id,
            'status_id' => $this->openStatusId,
        ]);
        LegalCase::factory()->create([
            'tenant_id' => $this->tenant->id,
            'status_id' => $closedStatus->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/cases?status={$this->openStatusId}");

        $response->assertStatus(200);
    }

    public function test_case_creation_requires_title(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/tenant/{$this->tenant->id}/cases", [
                'case_type' => 'Civil',
            ]);

        $response->assertStatus(422);
    }

    public function test_user_can_view_case_details(): void
    {
        $legalCase = LegalCase::factory()->create([
            'tenant_id' => $this->tenant->id,
            'status_id' => $this->openStatusId,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/cases/{$legalCase->id}");

        $response->assertStatus(200);
    }

    public function test_user_can_update_case(): void
    {
        $legalCase = LegalCase::factory()->create([
            'tenant_id' => $this->tenant->id,
            'status_id' => $this->openStatusId,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/tenant/{$this->tenant->id}/cases/{$legalCase->id}", [
                'title' => 'Updated Title',
                'case_type' => 'Criminal',
            ]);

        $response->assertStatus(200);
    }

    public function test_user_can_delete_case(): void
    {
        $legalCase = LegalCase::factory()->create([
            'tenant_id' => $this->tenant->id,
            'status_id' => $this->openStatusId,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/tenant/{$this->tenant->id}/cases/{$legalCase->id}");

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_cases(): void
    {
        $response = $this->getJson("/api/tenant/{$this->tenant->id}/cases");

        $response->assertStatus(401);
    }
}