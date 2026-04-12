<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class OtpLoginController extends Controller
{
    private SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function showLoginForm(): View
    {
        return view('auth.otp-login');
    }

    public function sendOtp(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'phone' => 'required|string|min:10',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        
        $user = User::where('phone', 'like', '%' . substr($phone, -10))->first();

        if (!$user) {
            return back()->with('error', 'No account found with this phone number.');
        }

        $result = $this->smsService->sendOtp($phone);

        if ($result['success']) {
            Session::put('otp_user_id', $user->id);
            Session::put('otp_phone', $phone);
            Session::put('otp_sent_at', now());
            
            return redirect()->route('otp.verify')->with('info', 'OTP sent to your phone.');
        }

        return back()->with('error', 'Failed to send OTP. Please try again.');
    }

    public function showVerifyForm(): \Illuminate\Http\Response
    {
        if (!Session::has('otp_user_id')) {
            return redirect()->route('otp.login');
        }

        return view('auth.otp-verify');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = Session::get('otp_user_id');
        $phone = Session::get('otp_phone');

        if (!$userId || !$phone) {
            return redirect()->route('otp.login')->with('error', 'Session expired. Please try again.');
        }

        if ($this->smsService->verifyOtp($phone, $request->otp)) {
            $user = User::findOrFail($userId);
            
            Auth::login($user);
            Session::forget(['otp_user_id', 'otp_phone', 'otp_sent_at']);

            return redirect()->intended(route('dashboard'))->with('success', 'Login successful!');
        }

        return back()->with('error', 'Invalid OTP. Please try again.');
    }

    public function resendOtp(): RedirectResponse
    {
        $phone = Session::get('otp_phone');

        if (!$phone) {
            return redirect()->route('otp.login');
        }

        $result = $this->smsService->sendOtp($phone);

        if ($result['success']) {
            Session::put('otp_sent_at', now());
            return back()->with('info', 'OTP resent to your phone.');
        }

        return back()->with('error', 'Failed to send OTP. Please try again.');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        Session::flush();
        return redirect('/');
    }
}
