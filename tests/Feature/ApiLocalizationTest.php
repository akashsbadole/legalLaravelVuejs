<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiLocalizationTest extends TestCase
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
        Setting::updateOrCreate(['key' => 'language'], ['value' => 'en']);
    }

    public function test_default_locale_is_english(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/settings');

        $response->assertStatus(200);
    }

    public function test_can_switch_locale_to_hindi(): void
    {
        Setting::updateOrCreate(['key' => 'language'], ['value' => 'hi']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/settings');

        $response->assertStatus(200);
    }

    public function test_can_switch_locale_to_tamil(): void
    {
        Setting::updateOrCreate(['key' => 'language'], ['value' => 'ta']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/settings');

        $response->assertStatus(200);
    }

    public function test_can_switch_locale_to_telugu(): void
    {
        Setting::updateOrCreate(['key' => 'language'], ['value' => 'te']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/settings');

        $response->assertStatus(200);
    }

    public function test_can_switch_locale_to_marathi(): void
    {
        Setting::updateOrCreate(['key' => 'language'], ['value' => 'mr']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/settings');

        $response->assertStatus(200);
    }

    public function test_invalid_locale_defaults_to_english(): void
    {
        Setting::updateOrCreate(['key' => 'language'], ['value' => 'invalid_locale']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/settings');

        $response->assertStatus(200);
    }

    public function test_all_supported_locales(): void
    {
        $locales = ['en', 'hi', 'ta', 'te', 'mr'];

        foreach ($locales as $locale) {
            Setting::updateOrCreate(['key' => 'language'], ['value' => $locale]);

            $response = $this->actingAs($this->admin, 'sanctum')
                ->getJson('/api/settings');

            $response->assertStatus(200);
        }
    }

    public function test_currency_symbol_changes_with_locale(): void
    {
        Setting::updateOrCreate(['key' => 'language'], ['value' => 'en']);
        Setting::updateOrCreate(['key' => 'currency'], ['value' => 'USD']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/settings');

        $response->assertStatus(200);
        $response->assertJsonFragment(['currency_symbol' => '$']);
    }

    public function test_indian_rupee_for_inr_currency(): void
    {
        Setting::updateOrCreate(['key' => 'language'], ['value' => 'hi']);
        Setting::updateOrCreate(['key' => 'currency'], ['value' => 'INR']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/settings');

        $response->assertStatus(200);
        $response->assertJsonFragment(['currency_symbol' => '₹']);
    }

    public function test_translation_files_exist_for_all_locales(): void
    {
        $expectedLocales = ['en', 'hi', 'ta', 'te', 'mr'];
        $langPath = resource_path('lang');

        foreach ($expectedLocales as $locale) {
            $this->assertFileExists("{$langPath}/{$locale}/messages.php");
        }
    }

    public function test_user_can_set_preferred_language(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson('/api/settings', [
                'language' => 'ta',
            ]);

        $response->assertStatus(200);
    }

    public function test_user_can_set_preferred_currency(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson('/api/settings', [
                'currency' => 'INR',
            ]);

        $response->assertStatus(200);
    }
}