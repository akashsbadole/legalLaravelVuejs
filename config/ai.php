<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Services Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AI services used in document management and analysis.
    | Multiple AI providers are supported with fallback mechanisms.
    |
    */

    'services' => [
        'gemini' => [
            'enabled' => env('GEMINI_ENABLED', true),
            'api_key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-1.5-flash-latest'),
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta/models/'),
            'timeout' => env('GEMINI_TIMEOUT', 30),
            'max_tokens' => env('GEMINI_MAX_TOKENS', 2048),
            'temperature' => env('GEMINI_TEMPERATURE', 0.3),
            'priority' => 2, // Higher priority than Claude
        ],

        'claude' => [
            'enabled' => env('CLAUDE_ENABLED', true),
            'api_key' => env('CLAUDE_API_KEY'),
            'model' => env('CLAUDE_MODEL', 'claude-3-sonnet-20240229'),
            'base_url' => env('CLAUDE_BASE_URL', 'https://api.anthropic.com/v1/messages'),
            'timeout' => env('CLAUDE_TIMEOUT', 45),
            'max_tokens' => env('CLAUDE_MAX_TOKENS', 2048),
            'temperature' => env('CLAUDE_TEMPERATURE', 0.3),
            'priority' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Preferences
    |--------------------------------------------------------------------------
    |
    | Define which AI service to prefer for specific tasks
    |
    */

    'preferences' => [
        'analysis' => 'claude', // Better for detailed legal analysis
        'summarization' => 'gemini', // Faster for quick summaries
        'generation' => 'claude', // Better for creative legal content
        'comparison' => 'claude', // Better for complex document comparisons
        'translation' => 'gemini', // Better multilingual support
        'compliance' => 'claude', // More thorough compliance checking
        'extraction' => 'gemini', // Better for structured data extraction
        'precedents' => 'claude', // Better for legal research
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limits for AI API calls to prevent overuse
    |
    */

    'rate_limits' => [
        'requests_per_hour' => env('AI_REQUESTS_PER_HOUR', 1000),
        'requests_per_minute' => env('AI_REQUESTS_PER_MINUTE', 50),
        'burst_limit' => env('AI_BURST_LIMIT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Cache AI responses to reduce API calls and improve performance
    |
    */

    'cache' => [
        'enabled' => env('AI_CACHE_ENABLED', true),
        'ttl' => env('AI_CACHE_TTL', 3600), // 1 hour
        'prefix' => 'ai_response:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Processing
    |--------------------------------------------------------------------------
    |
    | Settings for document processing and analysis
    |
    */

    'document_processing' => [
        'max_file_size' => env('AI_MAX_FILE_SIZE', 10 * 1024 * 1024), // 10MB
        'supported_formats' => ['pdf', 'doc', 'docx', 'txt', 'rtf'],
        'chunk_size' => env('AI_CHUNK_SIZE', 4000), // Characters per chunk
        'overlap' => env('AI_CHUNK_OVERLAP', 200), // Overlap between chunks
    ],

    /*
    |--------------------------------------------------------------------------
    | Legal Templates
    |--------------------------------------------------------------------------
    |
    | Predefined legal document templates
    |
    */

    'templates' => [
        'nda' => [
            'name' => 'Non-Disclosure Agreement',
            'category' => 'nda',
            'description' => 'Standard NDA template for protecting confidential information',
            'placeholders' => [
                'party_a_name', 'party_a_address', 'party_b_name', 'party_b_address',
                'effective_date', 'confidential_information', 'term', 'governing_law',
            ],
        ],

        'employment_contract' => [
            'name' => 'Employment Contract',
            'category' => 'employment_contract',
            'description' => 'Comprehensive employment agreement template',
            'placeholders' => [
                'employee_name', 'employee_address', 'employer_name', 'employer_address',
                'position', 'salary', 'start_date', 'benefits', 'termination_conditions',
            ],
        ],

        'service_agreement' => [
            'name' => 'Service Agreement',
            'category' => 'service_agreement',
            'description' => 'Service provision agreement template',
            'placeholders' => [
                'service_provider', 'client_name', 'services', 'payment_terms',
                'duration', 'termination', 'liability', 'governing_law',
            ],
        ],

        'lease_agreement' => [
            'name' => 'Lease Agreement',
            'category' => 'lease_agreement',
            'description' => 'Property lease agreement template',
            'placeholders' => [
                'landlord_name', 'tenant_name', 'property_address', 'lease_term',
                'rent_amount', 'security_deposit', 'maintenance', 'termination',
            ],
        ],

        'power_of_attorney' => [
            'name' => 'Power of Attorney',
            'category' => 'power_of_attorney',
            'description' => 'General power of attorney template',
            'placeholders' => [
                'principal_name', 'principal_address', 'attorney_name', 'attorney_address',
                'powers_granted', 'effective_date', 'termination_date', 'witnesses',
            ],
        ],

        'legal_notice' => [
            'name' => 'Legal Notice',
            'category' => 'legal_notice',
            'description' => 'Legal notice template for various disputes',
            'placeholders' => [
                'sender_name', 'sender_address', 'receiver_name', 'receiver_address',
                'subject', 'facts', 'legal_basis', 'relief_sought', 'deadline',
            ],
        ],

        'court_petition' => [
            'name' => 'Court Petition',
            'category' => 'court_petition',
            'description' => 'Court petition template for legal proceedings',
            'placeholders' => [
                'court_name', 'petitioner_name', 'respondent_name', 'petition_type',
                'facts', 'legal_provisions', 'prayer', 'place', 'date',
            ],
        ],

        'lease_deed' => [
            'name' => 'Lease Deed',
            'category' => 'lease_deed',
            'description' => 'Formal lease deed template',
            'placeholders' => [
                'lessor_name', 'lessee_name', 'property_description', 'lease_period',
                'rent_amount', 'stamp_duty', 'registration_details', 'witnesses',
            ],
        ],

        'sale_deed' => [
            'name' => 'Sale Deed',
            'category' => 'sale_deed',
            'description' => 'Property sale deed template',
            'placeholders' => [
                'seller_name', 'buyer_name', 'property_details', 'sale_price',
                'payment_terms', 'stamp_duty', 'registration', 'possession_date',
            ],
        ],

        'partnership_deed' => [
            'name' => 'Partnership Deed',
            'category' => 'partnership_deed',
            'description' => 'Partnership agreement template',
            'placeholders' => [
                'partners', 'firm_name', 'business_nature', 'capital_contribution',
                'profit_sharing', 'decision_making', 'dissolution', 'governing_law',
            ],
        ],

        'promissory_note' => [
            'name' => 'Promissory Note',
            'category' => 'promissory_note',
            'description' => 'Promissory note template for loans',
            'placeholders' => [
                'lender_name', 'borrower_name', 'principal_amount', 'interest_rate',
                'repayment_terms', 'due_date', 'collateral', 'signatures',
            ],
        ],

        'legal_letter' => [
            'name' => 'Legal Letter',
            'category' => 'legal_letter',
            'description' => 'General legal correspondence template',
            'placeholders' => [
                'sender_details', 'recipient_details', 'subject', 'reference',
                'body', 'enclosures', 'closing', 'sender_signature',
            ],
        ],

        'affidavit' => [
            'name' => 'Affidavit',
            'category' => 'affidavit',
            'description' => 'Affidavit template for court use',
            'placeholders' => [
                'deponent_name', 'deponent_address', 'deponent_age', 'deponent_occupation',
                'affidavit_content', 'place', 'date', 'notary_details',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security configurations for AI-powered document processing
    |
    */

    'security' => [
        'encrypt_documents' => env('AI_ENCRYPT_DOCUMENTS', true),
        'audit_logging' => env('AI_AUDIT_LOGGING', true),
        'content_filtering' => env('AI_CONTENT_FILTERING', true),
        'rate_limiting' => env('AI_RATE_LIMITING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics and Monitoring
    |--------------------------------------------------------------------------
    |
    | Settings for AI usage analytics and performance monitoring
    |
    */

    'analytics' => [
        'enabled' => env('AI_ANALYTICS_ENABLED', true),
        'track_requests' => env('AI_TRACK_REQUESTS', true),
        'track_performance' => env('AI_TRACK_PERFORMANCE', true),
        'alert_on_failures' => env('AI_ALERT_ON_FAILURES', true),
    ],
];
