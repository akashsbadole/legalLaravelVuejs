<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeAIService implements AIServiceInterface
{
    protected string $apiKey;

    protected string $baseUrl;

    protected string $model;

    protected int $timeout;

    protected int $maxTokens;

    protected float $temperature;

    public function __construct()
    {
        $config = config('ai.services.claude');
        $this->apiKey = $config['api_key'];
        $this->baseUrl = $config['base_url'];
        $this->model = $config['model'];
        $this->timeout = $config['timeout'];
        $this->maxTokens = $config['max_tokens'];
        $this->temperature = $config['temperature'];
    }

    public function analyzeDocument(string $content, array $options = []): array
    {
        $systemPrompt = $this->buildAnalysisSystemPrompt($options);
        $userPrompt = $this->buildAnalysisUserPrompt($content, $options);

        try {
            $response = Http::timeout($this->timeout)->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 1024,
                'temperature' => 0.2,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $this->parseAnalysisResponse($data);
            }

            Log::error('Claude AI analysis failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to analyze document',
                'insights' => [],
            ];
        } catch (\Exception $e) {
            Log::error('Claude AI analysis exception', [
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
        $systemPrompt = 'You are a legal document summarization expert. Provide concise, accurate summaries of legal documents.';
        $userPrompt = "Please provide a concise summary of the following legal document in {$maxLength} words or less, focusing on key terms, obligations, and important clauses:\n\n{$content}";

        try {
            $response = Http::timeout(30)->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 500,
                'temperature' => 0.3,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['content'][0]['text'] ?? 'Summary not available';
            }

            return 'Summary not available due to service error';
        } catch (\Exception $e) {
            return 'Summary not available due to service error';
        }
    }

    public function extractClauses(string $content): array
    {
        $systemPrompt = 'You are an expert legal document analyzer. Extract clauses and categorize them systematically.';
        $userPrompt = "Extract all clauses from the following legal document and categorize them. Return as JSON with categories like: definitions, obligations, rights, termination, governing_law, dispute_resolution, etc.\n\nDocument:\n{$content}\n\nReturn only valid JSON.";

        try {
            $response = Http::timeout(45)->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 1024,
                'temperature' => 0.2,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['content'][0]['text'] ?? '{}';

                // Clean and parse JSON
                $jsonText = $this->extractJsonFromText($text);

                return json_decode($jsonText, true) ?? ['error' => 'Failed to parse response'];
            }

            return ['error' => 'Failed to extract clauses'];
        } catch (\Exception $e) {
            return ['error' => 'Clause extraction failed'];
        }
    }

    public function checkCompliance(string $content, string $documentType): array
    {
        $systemPrompt = 'You are a legal compliance expert. Analyze documents for legal compliance, risks, and provide specific recommendations.';
        $userPrompt = "Review the following {$documentType} document for legal compliance issues. Focus on Indian law requirements, missing clauses, potential risks, and provide specific actionable recommendations:\n\n{$content}";

        try {
            $response = Http::timeout(45)->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 1024,
                'temperature' => 0.2,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $analysis = $data['content'][0]['text'] ?? '';

                return [
                    'compliance_score' => $this->calculateComplianceScore($analysis),
                    'issues' => $this->extractIssues($analysis),
                    'recommendations' => $this->extractRecommendations($analysis),
                    'risk_level' => $this->assessRiskLevel($analysis),
                    'detailed_analysis' => $analysis,
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
        $systemPrompt = 'You are a legal document generation expert. Create professional, legally sound document content.';
        $userPrompt = $this->buildGenerationPrompt($templateData, $userData);

        try {
            $response = Http::timeout(45)->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 2048,
                'temperature' => 0.7,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['content'][0]['text'] ?? '';
            }

            return '';
        } catch (\Exception $e) {
            return '';
        }
    }

    public function compareDocuments(string $content1, string $content2): array
    {
        $systemPrompt = 'You are a legal document comparison expert. Analyze differences between document versions.';
        $userPrompt = "Compare these two legal documents and provide a detailed analysis of differences:\n\nDocument 1:\n{$content1}\n\nDocument 2:\n{$content2}\n\nFocus on: additions, deletions, modifications, and their legal implications.";

        try {
            $response = Http::timeout(45)->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 1024,
                'temperature' => 0.2,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $comparison = $data['content'][0]['text'] ?? '';

                return [
                    'summary' => $comparison,
                    'differences' => $this->extractDifferences($comparison),
                    'similarity_score' => $this->calculateSimilarity($content1, $content2),
                    'legal_implications' => $this->extractLegalImplications($comparison),
                ];
            }

            return [
                'summary' => 'Comparison unavailable',
                'differences' => [],
                'similarity_score' => 0,
                'legal_implications' => [],
            ];
        } catch (\Exception $e) {
            return [
                'summary' => 'Comparison failed',
                'differences' => [],
                'similarity_score' => 0,
                'legal_implications' => [],
            ];
        }
    }

    public function translateDocument(string $content, string $targetLanguage): string
    {
        $systemPrompt = 'You are a legal translation expert. Translate documents while maintaining legal accuracy and terminology.';
        $userPrompt = "Translate the following legal document to {$targetLanguage}. Maintain legal terminology, formal tone, and ensure the translation is legally accurate:\n\n{$content}";

        try {
            $response = Http::timeout(45)->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 2048,
                'temperature' => 0.1,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['content'][0]['text'] ?? $content;
            }

            return $content;
        } catch (\Exception $e) {
            return $content;
        }
    }

    public function findPrecedents(string $content): array
    {
        $systemPrompt = 'You are a legal research expert specializing in Indian law. Identify relevant precedents and case laws.';
        $userPrompt = "Based on the following legal content, identify relevant Indian case laws, Supreme Court judgments, and legal precedents that may apply. Provide citations and brief explanations:\n\n{$content}";

        try {
            $response = Http::timeout(45)->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 1024,
                'temperature' => 0.3,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $suggestions = $data['content'][0]['text'] ?? '';

                return [
                    'precedents' => $this->parsePrecedents($suggestions),
                    'principles' => $this->extractPrinciples($suggestions),
                    'citations' => $this->extractCitations($suggestions),
                ];
            }

            return [
                'precedents' => [],
                'principles' => [],
                'citations' => [],
            ];
        } catch (\Exception $e) {
            return [
                'precedents' => [],
                'principles' => [],
                'citations' => [],
            ];
        }
    }

    public function getServiceName(): string
    {
        return 'Claude AI';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->apiKey);
    }

    public function getUsageStats(): array
    {
        return [
            'requests_today' => 0,
            'tokens_used' => 0,
            'cost_today' => 0.00,
        ];
    }

    protected function buildAnalysisSystemPrompt(array $options): string
    {
        $prompt = 'You are an expert legal document analyzer with deep knowledge of Indian law. Provide thorough, accurate analysis of legal documents.';

        if (isset($options['focus_areas'])) {
            $prompt .= ' Focus on: '.implode(', ', $options['focus_areas']);
        }

        return $prompt;
    }

    protected function buildAnalysisUserPrompt(string $content, array $options): string
    {
        $prompt = "Analyze this legal document and provide detailed insights in JSON format:\n\n{$content}\n\n";

        $prompt .= 'Return analysis with: key_findings, risks, recommendations, compliance_score (0-100), legal_issues, strengths';

        return $prompt;
    }

    protected function buildGenerationPrompt(array $templateData, array $userData): string
    {
        $prompt = "Generate professional legal document content using this information:\n\n";
        $prompt .= 'Document Type: '.($templateData['type'] ?? 'General')."\n";
        $prompt .= 'Template Structure: '.($templateData['structure'] ?? 'Standard legal format')."\n";
        $prompt .= 'User Data: '.json_encode($userData)."\n\n";
        $prompt .= 'Create comprehensive, legally sound content following Indian legal standards.';

        return $prompt;
    }

    protected function parseAnalysisResponse(array $data): array
    {
        $text = $data['content'][0]['text'] ?? '{}';

        try {
            // Try to extract JSON from the response
            $jsonText = $this->extractJsonFromText($text);

            return json_decode($jsonText, true) ?? [
                'success' => true,
                'analysis' => $text,
                'insights' => ['Analysis completed'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => true,
                'analysis' => $text,
                'insights' => ['Analysis completed'],
            ];
        }
    }

    protected function extractJsonFromText(string $text): string
    {
        $start = strpos($text, '{');
        $end = strrpos($text, '}');

        if ($start !== false && $end !== false && $end > $start) {
            return substr($text, $start, $end - $start + 1);
        }

        return $text;
    }

    protected function calculateComplianceScore(string $analysis): int
    {
        $score = 75; // Claude tends to be more thorough

        $positiveKeywords = ['compliant', 'adequate', 'comprehensive', 'properly'];
        $negativeKeywords = ['risk', 'missing', 'inadequate', 'concern', 'issue'];

        foreach ($positiveKeywords as $keyword) {
            if (stripos($analysis, $keyword) !== false) {
                $score += 5;
            }
        }

        foreach ($negativeKeywords as $keyword) {
            if (stripos($analysis, $keyword) !== false) {
                $score -= 8;
            }
        }

        return max(0, min(100, $score));
    }

    protected function extractIssues(string $analysis): array
    {
        $issues = [];
        $sentences = explode('.', $analysis);

        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (empty($sentence)) {
                continue;
            }

            // Look for sentences containing issue keywords
            $issueKeywords = ['risk', 'concern', 'missing', 'inadequate', 'problem', 'issue'];
            foreach ($issueKeywords as $keyword) {
                if (stripos($sentence, $keyword) !== false) {
                    $issues[] = $sentence;
                    break;
                }
            }
        }

        return array_slice($issues, 0, 5); // Limit to 5 issues
    }

    protected function extractRecommendations(string $analysis): array
    {
        $recommendations = [];
        $sentences = explode('.', $analysis);

        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (empty($sentence)) {
                continue;
            }

            // Look for sentences containing recommendation keywords
            $recKeywords = ['recommend', 'suggest', 'should', 'consider', 'advise'];
            foreach ($recKeywords as $keyword) {
                if (stripos($sentence, $keyword) !== false) {
                    $recommendations[] = $sentence;
                    break;
                }
            }
        }

        return array_slice($recommendations, 0, 5);
    }

    protected function assessRiskLevel(string $analysis): string
    {
        $highRiskKeywords = ['significant risk', 'high risk', 'critical', 'severe', 'major concern'];
        $mediumRiskKeywords = ['moderate risk', 'some concern', 'potential issue'];

        $analysisLower = strtolower($analysis);

        foreach ($highRiskKeywords as $keyword) {
            if (strpos($analysisLower, $keyword) !== false) {
                return 'high';
            }
        }

        foreach ($mediumRiskKeywords as $keyword) {
            if (strpos($analysisLower, $keyword) !== false) {
                return 'medium';
            }
        }

        return 'low';
    }

    protected function extractDifferences(string $comparison): array
    {
        $differences = [];
        $lines = explode("\n", $comparison);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (preg_match('/^(added|deleted|changed|modified)/i', $line)) {
                $differences[] = $line;
            }
        }

        return $differences;
    }

    protected function calculateSimilarity(string $content1, string $content2): float
    {
        // Use a more sophisticated similarity calculation
        $words1 = str_word_count(strtolower($content1), 1);
        $words2 = str_word_count(strtolower($content2), 1);

        $intersection = array_intersect($words1, $words2);
        $union = array_unique(array_merge($words1, $words2));

        return count($union) > 0 ? count($intersection) / count($union) : 0;
    }

    protected function extractLegalImplications(string $comparison): array
    {
        $implications = [];
        $sentences = explode('.', $comparison);

        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (empty($sentence)) {
                continue;
            }

            // Look for legal implication keywords
            $legalKeywords = ['legal effect', 'implication', 'consequence', 'impact', 'binding'];
            foreach ($legalKeywords as $keyword) {
                if (stripos($sentence, $keyword) !== false) {
                    $implications[] = $sentence;
                    break;
                }
            }
        }

        return $implications;
    }

    protected function parsePrecedents(string $suggestions): array
    {
        $precedents = [];
        $lines = explode("\n", $suggestions);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Look for case law patterns
            if (preg_match('/v\./i', $line) || preg_match('/case|judgment/i', $line)) {
                $precedents[] = $line;
            }
        }

        return $precedents;
    }

    protected function extractPrinciples(string $suggestions): array
    {
        $principles = [];
        $lines = explode("\n", $suggestions);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Look for legal principle keywords
            $principleKeywords = ['principle', 'doctrine', 'rule', 'law'];
            foreach ($principleKeywords as $keyword) {
                if (stripos($line, $keyword) !== false) {
                    $principles[] = $line;
                    break;
                }
            }
        }

        return $principles;
    }

    protected function extractCitations(string $suggestions): array
    {
        $citations = [];
        $lines = explode("\n", $suggestions);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Look for citation patterns (AIR, SCC, etc.)
            if (preg_match('/AIR|SCC|SC|HC|CrLJ/i', $line)) {
                $citations[] = $line;
            }
        }

        return $citations;
    }
}
