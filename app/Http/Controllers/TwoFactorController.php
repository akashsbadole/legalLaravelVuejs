<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();
        $google2fa = new Google2FA();
        
        if ($user->two_factor_enabled) {
            $qrCodeUrl = $user->getTwoFactorQrCodeUrl();
            return view('two-factor.setup', [
                'enabled' => true,
                'qrCodeUrl' => $qrCodeUrl,
                'recoveryCodes' => json_decode($user->two_factor_recovery_codes ?? '[]'),
            ]);
        }

        $secret = $google2fa->generateSecretKey();
        $qrCodeUrl = $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secret);
        
        session(['two_factor_secret' => $secret]);

        return view('two-factor.setup', [
            'enabled' => false,
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    public function enable(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $google2fa = new Google2FA();

        $secret = session('two_factor_secret');
        
        if (!$secret) {
            return redirect()->route('two-factor.setup')->with('error', 'Please start the setup process.');
        }

        $valid = $google2fa->verifyKey($secret, $request->code);
        
        if (!$valid) {
            return back()->with('error', 'Invalid verification code.');
        }

        $user->two_factor_secret = $secret;
        $user->two_factor_enabled = true;
        $user->two_factor_recovery_codes = json_encode($this->generateRecoveryCodes());
        $user->save();

        session()->forget('two_factor_secret');

        return redirect()->route('profile.edit')->with('success', 'Two-factor authentication enabled.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->two_factor_enabled) {
            return redirect()->route('profile.edit');
        }

        $user->disableTwoFactorAuthentication();

        return redirect()->route('profile.edit')->with('success', 'Two-factor authentication disabled.');
    }

    public function verify(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $google2fa = new Google2FA();

        $code = $request->code;
        $secret = $user->two_factor_secret;

        if ($google2fa->verifyKey($secret, $code)) {
            session(['two_factor_verified' => true]);
            return redirect()->intended(route('dashboard'));
        }

        return back()->with('error', 'Invalid verification code.');
    }

    private function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(\Illuminate\Support\Str::random(8));
        }
        return $codes;
    }
}
