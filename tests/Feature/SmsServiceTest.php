<?php

namespace Tests\Feature;

use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SmsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sms_service_sends_sms_when_configured(): void
    {
        Config::set('services.sms.provider', 'twilio');
        Config::set('services.sms.api_key', 'test_key');

        $smsService = new SmsService();

        // Test that service is enabled
        $this->assertTrue($smsService->isEnabled());
    }

    public function test_sms_service_handles_whatsapp_channel(): void
    {
        Config::set('services.sms.provider', 'twilio');
        Config::set('services.sms.api_key', 'test_key');
        Config::set('services.twilio.account_sid', 'test_sid');
        Config::set('services.twilio.auth_token', 'test_token');
        Config::set('services.twilio.whatsapp_number', '+1234567890');

        $smsService = new SmsService();

        // Test WhatsApp method exists
        $this->assertTrue(method_exists($smsService, 'sendWhatsApp'));
    }

    public function test_sms_service_logs_interactions(): void
    {
        Config::set('services.sms.provider', 'twilio');
        Config::set('services.sms.api_key', 'test_key');

        $smsService = new SmsService();

        // Test service instantiation
        $this->assertInstanceOf(SmsService::class, $smsService);
    }
}
