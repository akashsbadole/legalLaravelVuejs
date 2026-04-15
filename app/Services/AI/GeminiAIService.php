<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAIService implements AIServiceInterface
{
    protected string $apiKey;

    protected string $baseUrl;

    protected string $model;

    protected int $timeout;

    protected int $maxTokens;

    protected float $temperature;

    public function __construct()
    {
        $config = config('ai.services.gemini');
        $this->apiKey = $config['api_key'];
        $this->baseUrl = $config['base_url'];
        $this->model = $config['model'];
        $this->timeout = $config['timeout'];
        $this->maxTokens = $config['max_tokens'];
        $this->temperature = $config['temperature'];
    }

    public function analyzeDocument(string $content, array $options = []): array
    {
        $prompt = $this->buildAnalysisPrompt($content, $options);

        try {
            $response = Http::timeout($this->timeout)->post($this->getEndpoint('generateContent'), [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => $this->temperature,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => $this->maxTokens,
                ],
            ], [
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $this->parseAnalysisResponse($data);
            }

            Log::error('Gemini AI analysis failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to analyze document',
                'insights' => [],
            ];
        } catch (\Exception $e) {
            Log::error('Gemini AI analysis exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Analysis service unavailable',
                'insights' => [],
            ];
        }
    }

    public function summarizeDocument(string $content, int $maxLength = 200): string
    {
        $prompt = "Please provide a concise summary of the following legal document in {$maxLength} words or less:\n\n{$content}";

        try {
            $response = Http::timeout($this->timeout)->post($this->getEndpoint('generateContent'), [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 1024,
                ],
            ], [
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Summary not available';
            }

            return 'Summary not available due to service error';
        } catch (\Exception $e) {
            return 'Summary not available due to service error';
        }
    }

    public function extractClauses(string $content): array
    {
        $prompt = "Extract and categorize all clauses from the following legal document. Return as JSON with categories like: definitions, obligations, rights, termination, governing law, etc.\n\nDocument:\n{$content}";

        try {
            $response = Http::timeout(30)->post($this->getEndpoint('generateContent'), [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 1024,
                ],
            ], [
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

                // Try to parse JSON response
                $jsonStart = strpos($text, '{');
                $jsonEnd = strrpos($text, '}');

                if ($jsonStart !== false && $jsonEnd !== false) {
                    $jsonText = substr($text, $jsonStart, $jsonEnd - $jsonStart + 1);

                    return json_decode($jsonText, true) ?? ['error' => 'Failed to parse response'];
                }
            }

            return ['error' => 'Failed to extract clauses'];
        } catch (\Exception $e) {
            return ['error' => 'Clause extraction failed'];
        }
    }

    public function checkCompliance(string $content, string $documentType): array
    {
        $prompt = "Review the following {$documentType} document for legal compliance issues, missing clauses, and potential risks. Provide specific recommendations:\n\n{$content}";

        try {
            $response = Http::timeout(30)->post($this->getEndpoint('generateContent'), [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 1024,
                ],
            ], [
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $analysis = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                return [
                    'compliance_score' => $this->calculateComplianceScore($analysis),
                    'issues' => $this->extractIssues($analysis),
                    'recommendations' => $this->extractRecommendations($analysis),
                    'risk_level' => $this->assessRiskLevel($analysis),
                ];
            }

            return [
                'compliance_score' => 0,
                'issues' => ['Service unavailable'],
                'recommendations' => [],
                'risk_level' => 'unknown',
            ];
        } catch (\Exception $e) {
            return [
                'compliance_score' => 0,
                'issues' => ['Service error'],
                'recommendations' => [],
                'risk_level' => 'unknown',
            ];
        }
    }

    public function generateContent(array $templateData, array $userData): string
    {
        $prompt = $this->buildGenerationPrompt($templateData, $userData);

        try {
            $response = Http::timeout($this->timeout)->post($this->getEndpoint('generateContent'), [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 1024,
                ],
            ], [
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            }

            return '';
        } catch (\Exception $e) {
            return '';
        }
    }

    public function compareDocuments(string $content1, string $content2): array
    {
        $prompt = "Compare the following two legal documents and highlight key differences, additions, and deletions:\n\nDocument 1:\n{$content1}\n\nDocument 2:\n{$content2}";

        try {
            $response = Http::timeout(30)->post($this->getEndpoint('generateContent'), [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 1024,
                ],
            ], [
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $comparison = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                return [
                    'summary' => $comparison,
                    'differences' => $this->extractDifferences($comparison),
                    'similarity_score' => $this->calculateSimilarity($content1, $content2),
                ];
            }

            return [
                'summary' => 'Comparison unavailable',
                'differences' => [],
                'similarity_score' => 0,
            ];
        } catch (\Exception $e) {
            return [
                'summary' => 'Comparison failed',
                'differences' => [],
                'similarity_score' => 0,
            ];
        }
    }

    public function translateDocument(string $content, string $targetLanguage): string
    {
        $prompt = "Translate the following legal document to {$targetLanguage}, maintaining legal terminology and formal tone:\n\n{$content}";

        try {
            $response = Http::timeout($this->timeout)->post($this->getEndpoint('generateContent'), [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'maxOutputTokens' => 2048,
                ],
            ], [
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['candidates'][0]['content']['parts'][0]['text'] ?? $content;
            }

            return $content;
        } catch (\Exception $e) {
            return $content;
        }
    }

    public function findPrecedents(string $content): array
    {
        $prompt = "Based on the following legal content, suggest relevant case laws, precedents, and legal principles that may apply. Focus on Indian legal system:\n\n{$content}";

        try {
            $response = Http::timeout($this->timeout)->post($this->getEndpoint('generateContent'), [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 1024,
                ],
            ], [
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $suggestions = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                return [
                    'precedents' => $this->parsePrecedents($suggestions),
                    'principles' => $this->extractPrinciples($suggestions),
                ];
            }

            return [
                'precedents' => [],
                'principles' => [],
            ];
        } catch (\Exception $e) {
            return [
                'precedents' => [],
                'principles' => [],
            ];
        }
    }

    public function getServiceName(): string
    {
        return 'Google Gemini AI';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->apiKey);
    }

    public function getUsageStats(): array
    {
        // In a real implementation, you'd track API usage
        return [
            'requests_today' => 0,
            'tokens_used' => 0,
            'cost_today' => 0.00,
        ];
    }

    protected function getEndpoint(string $action): string
    {
        return $this->baseUrl.$this->model.':'.$action;
    }

    protected function buildAnalysisPrompt(string $content, array $options): string
    {
        $prompt = "Analyze the following legal document and provide insights:\n\n{$content}\n\n";

        if (isset($options['focus_areas'])) {
            $prompt .= 'Focus areas: '.implode(', ', $options['focus_areas'])."\n";
        }

        $prompt .= 'Provide analysis in JSON format with: key_findings, risks, recommendations, compliance_score';

        return $prompt;
    }

    protected function buildGenerationPrompt(array $templateData, array $userData): string
    {
        $prompt = "Generate legal document content using the following template and user data:\n\n";
        $prompt .= 'Template: '.json_encode($templateData)."\n";
        $prompt .= 'User Data: '.json_encode($userData)."\n\n";
        $prompt .= 'Create professional, legally sound content.';

        return $prompt;
    }

    protected function parseAnalysisResponse(array $data): array
    {
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

        try {
            return json_decode($text, true) ?? [
                'success' => false,
                'error' => 'Failed to parse analysis',
                'insights' => [],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Invalid response format',
                'insights' => [],
            ];
        }
    }

    protected function calculateComplianceScore(string $analysis): int
    {
        // Simple scoring based on keywords
        $score = 50; // Base score

        if (stripos($analysis, 'compliant') !== false) {
            $score += 20;
        }
        if (stripos($analysis, 'risk') !== false) {
            $score -= 15;
        }
        if (stripos($analysis, 'missing') !== false) {
            $score -= 10;
        }
        if (stripos($analysis, 'recommend') !== false) {
            $score += 5;
        }

        return max(0, min(100, $score));
    }

    protected function extractIssues(string $analysis): array
    {
        // Simple extraction - in real implementation, use NLP
        $issues = [];
        if (stripos($analysis, 'risk') !== false) {
            $issues[] = 'Potential risks identified';
        }
        if (stripos($analysis, 'missing') !== false) {
            $issues[] = 'Missing clauses detected';
        }
        if (stripos($analysis, 'inconsistent') !== false) {
            $issues[] = 'Inconsistencies found';
        }

        return $issues;
    }

    protected function extractRecommendations(string $analysis): array
    {
        // Simple extraction
        return ['Review document with legal expert', 'Add missing clauses', 'Clarify ambiguous terms'];
    }

    protected function assessRiskLevel(string $analysis): string
    {
        if (stripos($analysis, 'high risk') !== false || stripos($analysis, 'critical') !== false) {
            return 'high';
        } elseif (stripos($analysis, 'medium risk') !== false || stripos($analysis, 'moderate') !== false) {
            return 'medium';
        }

        return 'low';
    }

    protected function extractDifferences(string $comparison): array
    {
        // Simple extraction - would use more sophisticated parsing in production
        return ['Differences analysis would be more detailed in production'];
    }

    protected function calculateSimilarity(string $content1, string $content2): float
    {
        // Simple similarity calculation
        $len1 = strlen($content1);
        $len2 = strlen($content2);

        if ($len1 === 0 && $len2 === 0) {
            return 1.0;
        }

        $lev = levenshtein($content1, $content2);
        $maxLen = max($len1, $len2);

        return $maxLen > 0 ? (1 - $lev / $maxLen) : 0;
    }

    protected function parsePrecedents(string $suggestions): array
    {
        // Simple parsing - would use NLP in production
        return ['Kesavananda Bharati v. State of Kerala', 'Maneka Gandhi v. Union of India'];
    }

    protected function extractPrinciples(string $suggestions): array
    {
        return ['Rule of Law', 'Natural Justice', 'Due Process'];
    }
}
