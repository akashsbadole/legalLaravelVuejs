<?php

namespace Database\Seeders;

use App\Models\CaseStatus;
use App\Models\DocumentCategory;
use App\Models\ExpenseCategory;
use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Indian legal database
        $this->call([
            IpcSectionsSeeder::class,
            CrpcSectionsSeeder::class,
            CpcSectionsSeeder::class,
            ConstitutionArticlesSeeder::class,
            ChatbotDocumentCategorySeeder::class,
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@legalfirm.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'hourly_rate' => 250,
                'email_verified_at' => now(),
            ]
        );

        $attorney = User::updateOrCreate(
            ['email' => 'jane@legalfirm.com'],
            [
                'name' => 'Jane Attorney',
                'password' => bcrypt('password'),
                'role' => 'attorney',
                'hourly_rate' => 200,
                'email_verified_at' => now(),
            ]
        );

        $paralegal = User::updateOrCreate(
            ['email' => 'john@legalfirm.com'],
            [
                'name' => 'John Paralegal',
                'password' => bcrypt('password'),
                'role' => 'paralegal',
                'hourly_rate' => 75,
                'email_verified_at' => now(),
            ]
        );

        $client = User::updateOrCreate(
            ['email' => 'client@example.com'],
            [
                'name' => 'Test Client',
                'password' => bcrypt('password'),
                'role' => 'client',
                'email_verified_at' => now(),
            ]
        );

        $open = CaseStatus::create(['name' => 'Open', 'color' => '#28a745']);
        $pending = CaseStatus::create(['name' => 'Pending', 'color' => '#ffc107']);
        $closed = CaseStatus::create(['name' => 'Closed', 'color' => '#6c757d']);

        $legalCase = LegalCase::create([
            'case_number' => LegalCase::generateCaseNumber(),
            'title' => 'Smith v. Johnson',
            'description' => 'Civil litigation case regarding contract dispute',
            'case_type' => 'Civil',
            'status_id' => $open->id,
            'assigned_attorney_id' => $attorney->id,
            'open_date' => now()->subMonths(2),
            'expected_close_date' => now()->addMonths(4),
            'estimated_value' => 50000,
        ]);

        $legalCase->participants()->attach($client->id, ['role' => 'client']);

        DocumentCategory::create(['name' => 'Contracts']);
        DocumentCategory::create(['name' => 'Letters']);
        DocumentCategory::create(['name' => 'Motions']);

        ExpenseCategory::create(['name' => 'Court Fees', 'is_billable' => true]);
        ExpenseCategory::create(['name' => 'Travel', 'is_billable' => true]);
        ExpenseCategory::create(['name' => 'Research', 'is_billable' => true]);
    }
}
