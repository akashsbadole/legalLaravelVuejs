<?php

namespace App\Http\Controllers\Api\Legal;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use App\Models\IpcSection;
use App\Models\CrpcSection;
use App\Models\CpcSection;
use App\Models\ConstitutionArticle;
use App\Models\ChatbotDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function conversation(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255'
        ]);

        $conversation = ChatbotConversation::create([
            'title' => $request->title ?: 'New Legal Consultation',
            'user_id' => $request->user()?->id,
            'session_id' => $request->session_id ?: Str::uuid()->toString(),
            'status' => 'active'
        ]);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'session_id' => $conversation->session_id,
                'status' => $conversation->status,
                'message_count' => $conversation->message_count,
                'created_at' => $conversation->created_at
            ]
        ]);
    }

    public function conversations(Request $request)
    {
        $query = ChatbotConversation::query()
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->session_id, fn($q, $sessionId) => $q->where('session_id', $sessionId))
            ->orderBy('updated_at', 'desc');

        $conversations = $query->get()->map(function ($conv) {
            return [
                'id' => $conv->id,
                'title' => $conv->title,
                'preview' => $conv->messages()->latest()->first()?->message ?? '',
                'message_count' => $conv->message_count,
                'created_at' => $conv->created_at->toDateString(),
                'updated_at' => $conv->updated_at->toDateString()
            ];
        });

        return response()->json(['conversations' => $conversations]);
    }

    public function showConversation(ChatbotConversation $conversation)
    {
        $messages = $conversation->messages()->get()->map(function ($msg) {
            return [
                'id' => $msg->id,
                'sender' => $msg->sender,
                'message' => $msg->message,
                'references' => $msg->references,
                'is_bookmarked' => $msg->is_bookmarked,
                'created_at' => $msg->created_at
            ];
        });

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'session_id' => $conversation->session_id,
                'status' => $conversation->status,
                'message_count' => $conversation->message_count,
                'messages' => $messages,
                'created_at' => $conversation->created_at,
                'updated_at' => $conversation->updated_at
            ]
        ]);
    }

    public function updateConversation(Request $request, ChatbotConversation $conversation)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:active,completed,archived'
        ]);

        $conversation->update($request->only(['title', 'status']));

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'status' => $conversation->status,
                'updated_at' => $conversation->updated_at
            ]
        ]);
    }

    public function destroyConversation(ChatbotConversation $conversation)
    {
        $conversation->messages()->delete();
        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted successfully']);
    }

    public function message(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'conversation_id' => 'nullable|exists:chatbot_conversations,id',
            'session_id' => 'nullable|string'
        ]);

        $user = $request->user();
        
        $conversation = null;
        
        if ($request->conversation_id) {
            $conversation = ChatbotConversation::find($request->conversation_id);
        } elseif ($request->session_id) {
            $conversation = ChatbotConversation::where('session_id', $request->session_id)->first();
        }

        if (!$conversation) {
            $conversation = ChatbotConversation::create([
                'title' => 'Legal Consultation',
                'user_id' => $user?->id,
                'session_id' => $request->session_id ?: Str::uuid()->toString(),
                'status' => 'active'
            ]);
        }

        ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'user',
            'message' => $request->message
        ]);

        $conversation->incrementMessageCount();

        $response = $this->generateAIResponse($request->message, $conversation);

        ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'ai',
            'message' => $response['message'],
            'references' => $response['references']
        ]);

        $conversation->incrementMessageCount();
        $conversation->touch();

        return response()->json([
            'message' => $response['message'],
            'references' => $response['references'],
            'conversation' => [
                'id' => $conversation->id,
                'session_id' => $conversation->session_id,
                'title' => $conversation->title,
                'message_count' => $conversation->message_count
            ]
        ])->withHeaders([
            'X-RateLimit-Remaining' => 50,
            'X-RateLimit-Reset' => time() + 3600
        ]);
    }

    protected function generateAIResponse(string $userMessage, ChatbotConversation $conversation): array
    {
        $message = strtolower($userMessage);
        $references = [];

        if (preg_match('/section\s*(\d+)/i', $userMessage, $matches) || preg_match('/(\d+)/', $userMessage, $matches)) {
            $sectionNumber = $matches[1];
            
            if (stripos($message, 'ipc') !== false || stripos($message, 'murder') !== false || stripos($message, 'theft') !== false || stripos($message, 'cheating') !== false || stripos($message, 'hurt') !== false) {
                $ipc = IpcSection::where('section_number', $sectionNumber)->first();
                if ($ipc) {
                    $references[] = [
                        'type' => 'ipc',
                        'id' => $ipc->id,
                        'section_number' => $ipc->section_number,
                        'title' => $ipc->title,
                        'description' => $ipc->description
                    ];
                }
            }
            
            if (stripos($message, 'crpc') !== false || stripos($message, 'bailable') !== false || stripos($message, 'arrest') !== false) {
                $crpc = CrpcSection::where('section_number', $sectionNumber)->first();
                if ($crpc) {
                    $references[] = [
                        'type' => 'crpc',
                        'id' => $crpc->id,
                        'section_number' => $crpc->section_number,
                        'title' => $crpc->title,
                        'description' => $crpc->description
                    ];
                }
            }
            
            if (stripos($message, 'cpc') !== false || stripos($message, 'civil') !== false) {
                $cpc = CpcSection::where('section_number', $sectionNumber)->first();
                if ($cpc) {
                    $references[] = [
                        'type' => 'cpc',
                        'id' => $cpc->id,
                        'section_number' => $cpc->section_number,
                        'title' => $cpc->title,
                        'description' => $cpc->description
                    ];
                }
            }
        }

        if (stripos($message, 'article') !== false || stripos($message, 'fundamental') !== false || stripos($message, 'constitution') !== false || stripos($message, 'right') !== false) {
            $articleNumber = null;
            if (preg_match('/article\s*(\d+)/i', $userMessage, $matches)) {
                $articleNumber = $matches[1];
            }
            
            if ($articleNumber) {
                $article = ConstitutionArticle::where('article_number', $articleNumber)->first();
            } else {
                $article = ConstitutionArticle::where('title', 'like', "%{$userMessage}%")->first();
            }
            
            if ($article) {
                $references[] = [
                    'type' => 'constitution',
                    'id' => $article->id,
                    'article_number' => $article->article_number,
                    'title' => $article->title,
                    'description' => $article->description
                ];
            }
        }

        $responseText = $this->buildResponseText($userMessage, $references);

        return [
            'message' => $responseText,
            'references' => $references
        ];
    }

    protected function buildResponseText(string $userMessage, array $references): string
    {
        $message = strtolower($userMessage);
        
        if (stripos($message, 'murder') !== false || stripos($message, '302') !== false) {
            return "Section 302 of the Indian Penal Code deals with punishment for murder. According to this section, whoever commits murder shall be punished with death, or imprisonment for life, and shall also be liable to fine.\n\nThis is a non-bailable, cognizable offense investigated by police. The offense is compoundable only in cases where the victim survives and compounding is permitted under Section 320 CrPC.\n\nKey points:\n- Punishment: Death or life imprisonment\n- Cognizable: Yes (police can arrest without warrant)\n- Bailable: No\n- Trial: By Session Court";
        }
        
        if (stripos($message, 'theft') !== false || stripos($message, '378') !== false || stripos($message, '379') !== false) {
            return "Section 378 IPC defines theft as \"Whoever, intending to take dishonestly any movable property out of the possession of any person without that person's consent, moves that property in order to such taking, is said to commit theft.\"\n\nThe punishment under Section 379 IPC is imprisonment up to 3 years, or fine, or both. This is a bailable, cognizable offense.\n\nKey points:\n- Punishment: Up to 3 years imprisonment\n- Cognizable: Yes\n- Bailable: Yes\n- Compoundable: Yes (with permission of court)";
        }
        
        if (stripos($message, 'cheating') !== false || stripos($message, '420') !== false) {
            return "Section 420 of the Indian Penal Code deals with cheating and dishonestly inducing delivery of property. The punishment is imprisonment up to 7 years and fine.\n\nKey elements:\n1. Representation of fact as true\n2. Fact must be false\n3. Defendant knew it to be false\n4. Defendant's intention to deceive\n5. Induced delivery of property";
        }
        
        if (stripos($message, 'fundamental right') !== false || stripos($message, 'article 14') !== false || stripos($message, 'equality') !== false) {
            return "Article 14 of the Indian Constitution guarantees equality before law and equal protection of laws. This is one of the fundamental rights enshrined in Part III of the Constitution.\n\nThe Supreme Court has interpreted this article extensively, establishing the doctrine of reasonable classification and prohibiting arbitrary state action.\n\nKey points:\n- Equality before law ( Formal equality)\n- Equal protection of laws (Substantive equality)\n- Reasonable classification is permitted\n- Arbitrary state action is prohibited";
        }
        
        if (stripos($message, 'article 21') !== false || stripos($message, 'right to life') !== false) {
            return "Article 21 of the Indian Constitution states: \"No person shall be deprived of his life or personal liberty except according to procedure established by law.\"\n\nThis has been interpreted broadly by the Supreme Court to include:\n- Right to live with dignity\n- Right to privacy\n- Right to clean environment\n- Right to health\n- Right to education\n\nIt also includes protection against arbitrary detention.";
        }
        
        if (stripos($message, 'bailable') !== false || stripos($message, 'non-bailable') !== false) {
            return "Under CrPC, offenses are classified as:\n\n**Bailable Offenses**: Where the accused has a right to be released on bail as a matter of right (Section 2(a)).\n\n**Non-Bailable Offenses**: Where bail is granted at the discretion of the court (Section 2(a)).\n\nThe Schedule to CrPC lists bailable and non-bailable offenses. Generally:\n- Compoundable offenses are bailable\n- Serious crimes (murder, rape, etc.) are non-bailable\n- The court considers factors like severity, evidence, flight risk";
        }
        
        if (stripos($message, 'arrest') !== false || stripos($message, 'detention') !== false) {
            return "Under CrPC, police can arrest:\n\n1. **Without warrant** (Section 41): For cognizable offenses\n2. **With warrant** (Section 70): For non-cognizable offenses\n\n**Detention Limits**:\n- Up to 24 hours without magistrate approval\n- Further detention requires magistrate's permission (Section 167)\n- Maximum 90 days (or 60 days for non-death penalty cases) for chargesheet filing\n- For crimes punishable by death: up to 90 days before magistrate\n\n**Rights of Arrested Person**:\n- Right to be informed of grounds of arrest\n- Right to consult a lawyer\n- Right to be produced before magistrate within 24 hours";
        }
        
        if (stripos($message, 'compound') !== false || stripos($message, 'compounding') !== false) {
            return "Under Section 320 CrPC, certain offenses are compoundable (accused and victim can settle the case):\n\n**Compoundable with Permission**:\n- Hurt (Section 323)\n- Wrongful confinement (Section 341)\n- Criminal trespass (Section 447)\n- Cheating (Section 420)\n\n**Compoundable without Permission**:\n- Simple hurt (Section 323)\n- Defamation (Section 500)\n- Adultery (Section 497 - now decriminalized)\n\n**Not Compoundable**:\n- Murder (Section 302)\n- Rape\n- Offenses against state\n\nCompounding is only valid if: (a) no aggrieved party under 18, (b) consent of court obtained.";
        }
        
        if (stripos($message, 'fraud') !== false) {
            return "Under IPC, fraud is primarily covered under:\n\n- **Section 420**: Cheating and dishonestly inducing delivery of property (imprisonment up to 7 years)\n- **Section 467**: Forgery of valuable security (imprisonment up to life)\n- **Section 468**: Forgery for purpose of cheating (imprisonment up to 7 years)\n\n**Essential elements of fraud**:\n1. False representation of fact\n2. Person making representation knows it to be false\n3. Intention to deceive\n4. Another person induced to act\n\n**Civil vs Criminal Fraud**: Criminal fraud requires dishonest intention, while civil fraud focuses on damages.";
        }
        
        if (stripos($message, 'defamation') !== false || stripos($message, 'slander') !== false) {
            return "Defamation in India is covered under:\n\n- **Section 499 IPC**: Defamation (criminal)\n- **Section 500 IPC**: Punishment (imprisonment up to 2 years or fine or both)\n\n**Defenses available**:\n1. Truth (justification)\n2. Good faith\n3. Opinion (fair comment)\n4. Privileged communication (proceedings of Parliament/State Legislature)\n\n**Civil Defamation**: Also actionable under law of torts.\n\n**Key points**:\n- Complaint required from aggrieved person\n- Compoundable offense\n- Truth is absolute defense";
        }
        
        if (stripos($message, 'rape') !== false || stripos($message, '376') !== false) {
            return "Section 376 IPC defines punishment for rape:\n\n**Standard Punishment**: Rigorous imprisonment for not less than 10 years (extendable to life)\n\n**Aggravated Cases** (where victim is minor, relative, police officer, etc.): Not less than 20 years or life\n\n**Key Changes after 2013 Amendment**:\n- Marital rape recognized (with exceptions)\n- Definition expanded to include penetration of any object\n- Stringent punishment including death penalty in some cases\n\n**Other Protections**:\n- Survivor identity protection\n- In-camera trials\n- No character evidence allowed";
        }
        
        if (!empty($references)) {
            $typeLabels = [
                'ipc' => 'IPC Section',
                'crpc' => 'CrPC Section', 
                'cpc' => 'CPC Order/Section',
                'constitution' => 'Constitutional Article'
            ];
            
            $refList = collect($references)->map(fn($r) => "• **" . ($typeLabels[$r['type']] ?? $r['type']) . " " . ($r['section_number'] ?? $r['article_number']) . "**: " . $r['title'])->implode("\n");
            
            return "Based on your query, I found the following relevant legal references:\n\n{$refList}\n\nFor more detailed information, please check the specific sections mentioned above.";
        }

        return "I understand you're asking about \"{$userMessage}\". While I have access to Indian legal statutes including IPC, CrPC, CPC, and the Constitution, I need more specific details to provide accurate information.\n\nCould you please clarify:\n- Are you looking for a specific section or article?\n- Is this related to criminal law, civil procedure, or constitutional law?\n- What specific aspect would you like to know about?\n\nRemember, this is for informational purposes only and not legal advice.";
    }
}