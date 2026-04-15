<?php

namespace App\Services\AI;

interface AIServiceInterface
{
    /**
     * Analyze document content and provide insights
     */
    public function analyzeDocument(string $content, array $options = []): array;

    /**
     * Summarize document content
     */
    public function summarizeDocument(string $content, int $maxLength = 200): string;

    /**
     * Extract clauses and sections from legal document
     */
    public function extractClauses(string $content): array;

    /**
     * Check document for compliance issues
     */
    public function checkCompliance(string $content, string $documentType): array;

    /**
     * Generate document content based on template and data
     */
    public function generateContent(array $templateData, array $userData): string;

    /**
     * Compare two document versions
     */
    public function compareDocuments(string $content1, string $content2): array;

    /**
     * Translate legal document to specified language
     */
    public function translateDocument(string $content, string $targetLanguage): string;

    /**
     * Find legal precedents related to content
     */
    public function findPrecedents(string $content): array;

    /**
     * Get AI service name
     */
    public function getServiceName(): string;

    /**
     * Check if service is available
     */
    public function isAvailable(): bool;

    /**
     * Get usage statistics
     */
    public function getUsageStats(): array;
}
