<?php

namespace Database\Seeders;

use App\Models\ChatbotDocumentCategory;
use Illuminate\Database\Seeder;

class ChatbotDocumentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Training Data',
                'slug' => 'training_data',
                'description' => 'Documents used to train the AI chatbot',
                'icon' => '🎓',
                'color' => '#10B981',
                'category_type' => 'training',
                'allowed_file_types' => ['pdf', 'doc', 'docx', 'txt', 'json'],
                'max_file_size' => 52428800, // 50MB
                'auto_process' => true,
                'requires_approval' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Conversation Logs',
                'slug' => 'conversation_logs',
                'description' => 'Recorded conversations with the chatbot for analysis',
                'icon' => '💬',
                'color' => '#3B82F6',
                'category_type' => 'logs',
                'allowed_file_types' => ['json', 'txt', 'csv'],
                'max_file_size' => 10485760, // 10MB
                'auto_process' => false,
                'requires_approval' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Knowledge Base',
                'slug' => 'knowledge_base',
                'description' => 'Reference documents and knowledge sources',
                'icon' => '📚',
                'color' => '#8B5CF6',
                'category_type' => 'documentation',
                'allowed_file_types' => ['pdf', 'doc', 'docx', 'txt', 'html'],
                'max_file_size' => 26214400, // 25MB
                'auto_process' => true,
                'requires_approval' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Legal References',
                'slug' => 'legal_references',
                'description' => 'Indian legal database references (IPC, CrPC, CPC, Constitution)',
                'icon' => '⚖️',
                'color' => '#DC2626',
                'category_type' => 'documentation',
                'allowed_file_types' => ['json'],
                'max_file_size' => 1048576, // 1MB
                'auto_process' => false,
                'requires_approval' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Configuration',
                'slug' => 'configuration',
                'description' => 'Chatbot configuration files and settings',
                'icon' => '⚙️',
                'color' => '#6B7280',
                'category_type' => 'configuration',
                'allowed_file_types' => ['json', 'yaml', 'yml', 'txt'],
                'max_file_size' => 1048576, // 1MB
                'auto_process' => false,
                'requires_approval' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'API Documentation',
                'slug' => 'api_documentation',
                'description' => 'API documentation and integration guides',
                'icon' => '📋',
                'color' => '#059669',
                'category_type' => 'documentation',
                'allowed_file_types' => ['pdf', 'doc', 'docx', 'html', 'md'],
                'max_file_size' => 10485760, // 10MB
                'auto_process' => false,
                'requires_approval' => false,
                'sort_order' => 6,
            ],
            [
                'name' => 'Analytics Reports',
                'slug' => 'analytics_reports',
                'description' => 'Performance analytics and usage reports',
                'icon' => '📊',
                'color' => '#D97706',
                'category_type' => 'analytics',
                'allowed_file_types' => ['pdf', 'xlsx', 'csv', 'json'],
                'max_file_size' => 20971520, // 20MB
                'auto_process' => false,
                'requires_approval' => false,
                'sort_order' => 7,
            ],
        ];

        foreach ($categories as $category) {
            ChatbotDocumentCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
