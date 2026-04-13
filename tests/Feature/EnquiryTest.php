<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    public function test_enquiry_submission(): void
    {
        $enquiryData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'category' => 'civil_law',
            'priority' => 'normal',
            'message' => 'I need help with a civil law matter.',
        ];

        $response = $this->post('/contact', $enquiryData);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('enquiries', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'category' => 'civil_law',
            'priority' => 'normal',
            'status' => 'new',
        ]);
    }

    public function test_enquiry_validation(): void
    {
        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'category' => 'invalid_category',
            'message' => '',
        ];

        $response = $this->post('/contact', $invalidData);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['name', 'email', 'category', 'message']);
    }

    public function test_enquiry_assignment(): void
    {
        $enquiry = Enquiry::factory()->create(['status' => 'new']);
        $lawyer = User::factory()->create(['role' => 'attorney']);

        $this->actingAs($lawyer);

        $response = $this->post("/admin/enquiries/{$enquiry->id}/assign", [
            'assigned_to' => $lawyer->id,
            'admin_notes' => 'Assigned for review',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enquiries', [
            'id' => $enquiry->id,
            'assigned_to' => $lawyer->id,
            'status' => 'assigned',
            'admin_notes' => 'Assigned for review',
        ]);
    }

    public function test_enquiry_status_update(): void
    {
        $enquiry = Enquiry::factory()->create(['status' => 'assigned']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $response = $this->post("/admin/enquiries/{$enquiry->id}/status", [
            'status' => 'resolved',
            'admin_notes' => 'Case resolved successfully',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enquiries', [
            'id' => $enquiry->id,
            'status' => 'resolved',
            'admin_notes' => 'Case resolved successfully',
        ]);
        $this->assertNotNull($enquiry->fresh()->resolved_at);
    }

    public function test_enquiry_priority_filtering(): void
    {
        Enquiry::factory()->create(['priority' => 'urgent']);
        Enquiry::factory()->create(['priority' => 'normal']);
        Enquiry::factory()->create(['priority' => 'emergency']);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get('/admin/enquiries?priority=urgent');

        $response->assertStatus(200);
        $response->assertViewHas('enquiries');
        $this->assertEquals(1, $response->viewData('enquiries')->count());
    }
}
