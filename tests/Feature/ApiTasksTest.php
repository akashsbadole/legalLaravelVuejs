<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTasksTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role' => 'attorney',
        ]);
    }

    public function test_user_can_list_tasks(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/tenant/{$this->tenant->id}/tasks");

        $response->assertStatus(200);
    }

    public function test_task_creation_requires_title(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/tenant/{$this->tenant->id}/tasks", []);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_user_cannot_access_tasks(): void
    {
        $response = $this->getJson("/api/tenant/{$this->tenant->id}/tasks");

        $response->assertStatus(401);
    }
}