<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\UserInvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function index(): JsonResponse
    {
        $invitations = Invitation::with('invitedBy')->latest()->get();
        return response()->json($invitations);
    }

    public function invite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|in:admin,attorney,paralegal,clerk',
        ]);

        $invitation = Invitation::create([
            'email' => $validated['email'],
            'role' => $validated['role'],
            'token' => Str::random(40),
            'invited_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        try {
            Mail::to($validated['email'])->send(new UserInvitationMail($invitation));
        } catch (\Exception $e) {
            // Log the error but continue for now, as SMTP might not be configured
            \Illuminate\Support\Facades\Log::error('Invitation email failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Invitation sent successfully',
            'data' => $invitation
        ], 201);
    }

    public function verify(string $token): JsonResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return response()->json(['message' => 'Invitation is invalid or expired'], 422);
        }

        return response()->json($invitation);
    }

    public function accept(Request $request, string $token): JsonResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return response()->json(['message' => 'Invitation is invalid or expired'], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $invitation->email,
            'password' => Hash::make($validated['password']),
            'role' => $invitation->role,
            'tenant_id' => $invitation->tenant_id,
            'email_verified_at' => now(),
        ]);

        $invitation->update(['accepted_at' => now()]);

        return response()->json([
            'message' => 'Invitation accepted and account created',
            'user' => $user
        ]);
    }
}
