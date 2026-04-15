<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function message(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
            'conversation_id' => 'nullable|exists:chatbot_conversations,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $message = trim($request->message);

        // Rate limiting check
        $rateLimitKey = "chatbot_rate_limit_{$user->id}";
        $currentRequests = Cache::get($rateLimitKey, 0);

        if ($currentRequests >= 50) { // 50 messages per hour
            return response()->json(['message' => 'Rate limit exceeded. Please try again later.'], 429);
        }

        // Find or create conversation
        $conversation = null;
        if ($request->conversation_id) {
            $conversation = ChatbotConversation::where('id', $request->conversation_id)
                ->where('user_id', $user->id)
                ->first();

            if (! $conversation) {
                return response()->json(['message' => 'Conversation not found'], 404);
            }
        } else {
            $conversation = ChatbotConversation::create([
                'title' => 'New Legal Consultation',
                'user_id' => $user->id,
                'session_id' => Str::random(32),
                'status' => 'active',
                'message_count' => 0,
            ]);
        }

        // Save user message
        ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'user',
            'message' => $message,
            'references' => [],
        ]);

        $conversation->incrementMessageCount();

        // Generate AI response
        $aiResponse = $this->generateAIResponse($message);

        // Save AI response
        $aiMessage = ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'assistant',
            'message' => $aiResponse['message'],
            'references' => $aiResponse['references'],
        ]);

        $conversation->incrementMessageCount();

        // Update rate limit
        Cache::put($rateLimitKey, $currentRequests + 1, now()->addHour());

        return response()->json([
            'message' => $aiResponse['message'],
            'references' => $aiResponse['references'],
            'conversation' => $conversation->load('messages'),
        ], 200, [
            'X-RateLimit-Remaining' => 49 - $currentRequests,
            'X-RateLimit-Reset' => now()->addHour()->timestamp,
        ]);
    }

    public function conversation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $conversation = ChatbotConversation::create([
            'title' => $request->title ?: 'New Legal Consultation',
            'user_id' => auth()->id(),
            'session_id' => Str::random(32),
            'status' => 'active',
            'message_count' => 0,
        ]);

        return response()->json(['conversation' => $conversation], 201);
    }

    public function conversations(Request $request): JsonResponse
    {
        $conversations = ChatbotConversation::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return response()->json(['conversations' => $conversations]);
    }

    public function showConversation($conversationId): JsonResponse
    {
        $conversation = ChatbotConversation::where('id', $conversationId)
            ->where('user_id', auth()->id())
            ->with('messages')
            ->first();

        if (! $conversation) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        return response()->json(['conversation' => $conversation]);
    }

    public function updateConversation(Request $request, $conversationId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $conversation = ChatbotConversation::where('id', $conversationId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $conversation) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        $conversation->update(['title' => $request->title]);

        return response()->json(['conversation' => $conversation]);
    }

    public function destroyConversation($conversationId): JsonResponse
    {
        $conversation = ChatbotConversation::where('id', $conversationId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $conversation) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted']);
    }

    private function generateAIResponse(string $message): array
    {
        $message = strtolower($message);
        $response = '';
        $references = [];

        // Legal knowledge base responses
        if (str_contains($message, 'murder') || str_contains($message, '302')) {
            $response = 'Section 302 of the Indian Penal Code deals with punishment for murder. According to this section, whoever commits murder shall be punished with death, or imprisonment for life, and shall also be liable to fine.

This is a non-bailable, cognizable offense investigated by police. The offense is compoundable only in cases where the victim survives and compounding is permitted under Section 320 CrPC.';

            $references = [
                [
                    'id' => 'ipc_302',
                    'type' => 'ipc',
                    'section_number' => 302,
                    'title' => 'Punishment for murder',
                    'description' => 'Whoever commits murder shall be punished with death, or imprisonment for life...',
                ],
            ];
        } elseif (str_contains($message, 'theft') || str_contains($message, '378')) {
            $response = "Section 378 IPC defines theft as \"Whoever, intending to take dishonestly any movable property out of the possession of any person without that person's consent, moves that property in order to such taking, is said to commit theft.\"

The punishment under Section 379 IPC is imprisonment up to 3 years, or fine, or both. This is a bailable offense.";

            $references = [
                [
                    'id' => 'ipc_378',
                    'type' => 'ipc',
                    'section_number' => 378,
                    'title' => 'Theft',
                    'description' => 'Definition of theft under IPC...',
                ],
            ];
        } elseif (str_contains($message, 'fundamental rights') || str_contains($message, 'article 14')) {
            $response = 'Article 14 of the Indian Constitution guarantees equality before law and equal protection of laws. This is one of the fundamental rights enshrined in Part III of the Constitution.

The Supreme Court has interpreted this article extensively, establishing the doctrine of reasonable classification and prohibiting arbitrary state action.';

            $references = [
                [
                    'id' => 'constitution_14',
                    'type' => 'constitution',
                    'article_number' => '14',
                    'title' => 'Equality before law',
                    'description' => 'The State shall not deny to any person equality before the law...',
                ],
            ];
        } elseif (str_contains($message, 'crpc') || str_contains($message, 'criminal procedure')) {
            $response = 'The Code of Criminal Procedure (CrPC) is the procedural law in India that provides the machinery for the investigation of crime, apprehension of suspected criminals, collection of evidence, determination of guilt or innocence of the accused person and the determination of punishment of the guilty.

Key sections include:
- Section 41: Power to arrest without warrant
- Section 91: Summons to produce document
- Section 164: Recording of confessions
- Section 173: Police report on completion of investigation';

            $references = [
                [
                    'id' => 'crpc_overview',
                    'type' => 'crpc',
                    'title' => 'Code of Criminal Procedure Overview',
                    'description' => 'Procedural law for criminal cases in India...',
                ],
            ];
        } elseif (str_contains($message, 'cpc') || str_contains($message, 'civil procedure')) {
            $response = 'The Code of Civil Procedure (CPC) is the procedural law in India that governs the procedure to be followed in civil courts. It was enacted in 1908 and has been amended several times.

Key sections include:
- Section 9: Courts to try all civil suits unless barred
- Section 80: Notice to Government servants
- Section 96: Appeal from original decree
- Section 115: Revision by High Court';

            $references = [
                [
                    'id' => 'cpc_overview',
                    'type' => 'cpc',
                    'title' => 'Code of Civil Procedure Overview',
                    'description' => 'Procedural law for civil cases in India...',
                ],
            ];
        } else {
            $response = "I understand you're asking about \"{$message}\". While I have access to Indian legal statutes including IPC, CrPC, CPC, and the Constitution, I need more specific details to provide accurate information.

Could you please clarify:
• Are you looking for a specific section or article?
• Is this related to criminal law, civil procedure, or constitutional law?
• What specific aspect would you like to know about?

Remember, this is for informational purposes only and not legal advice. Please consult qualified legal professionals for specific legal matters.";

            $references = [
                [
                    'id' => 'general_guidance',
                    'type' => 'general',
                    'title' => 'General Legal Guidance',
                    'description' => 'Disclaimer and guidance for legal inquiries...',
                ],
            ];
        }

        return [
            'message' => $response,
            'references' => $references,
        ];
    }
}
