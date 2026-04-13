<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('landing');
    }

    public function test_health_check_endpoint(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'healthy',
                    'services' => [
                        'database' => 'connected',
                        'cache' => 'connected',
                        'storage' => 'writable',
                    ],
                    'tables' => 'exist',
                ]);
    }

    public function test_user_can_register(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_roles_work(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $attorney = User::factory()->create(['role' => 'attorney']);
        $client = User::factory()->create(['role' => 'client']);

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($attorney->isAttorney());
        $this->assertFalse($client->isAdmin());
        $this->assertFalse($client->isAttorney());
    }

    public function test_password_reset_request(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    public function test_rate_limiting_on_login(): void
    {
        // Test that multiple failed login attempts are rate limited
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email' => 'nonexistent@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response->assertStatus(429); // Too Many Requests
    }
}
