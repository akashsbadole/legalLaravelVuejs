<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $messages = Message::with(['sender', 'receiver', 'case'])
            ->where(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })
            ->latest()
            ->paginate($request->get('per_page', 20));

        return response()->json($messages);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'case_id' => 'nullable|exists:cases,id',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'case_id' => $validated['case_id'] ?? null,
            'content' => $validated['content'],
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        return response()->json($message->load(['sender', 'receiver']), 201);
    }

    public function show(Message $message): JsonResponse
    {
        if ($message->receiver_id === auth()->id()) {
            $message->markAsRead();
        }

        return response()->json($message->load(['sender', 'receiver', 'case']));
    }

    public function getConversation(User $user): JsonResponse
    {
        $authId = auth()->id();
        $messages = Message::where(function($q) use ($authId, $user) {
            $q->where('sender_id', $authId)->where('receiver_id', $user->id);
        })->orWhere(function($q) use ($authId, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $authId);
        })->oldest()->get();

        return response()->json($messages);
    }
}
