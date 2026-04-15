<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Case permissions
            ['name' => 'view_cases', 'description' => 'View cases', 'module' => 'cases'],
            ['name' => 'create_cases', 'description' => 'Create new cases', 'module' => 'cases'],
            ['name' => 'edit_cases', 'description' => 'Edit existing cases', 'module' => 'cases'],
            ['name' => 'delete_cases', 'description' => 'Delete cases', 'module' => 'cases'],
            ['name' => 'assign_cases', 'description' => 'Assign cases to staff', 'module' => 'cases'],

            // Client permissions
            ['name' => 'view_clients', 'description' => 'View client profiles', 'module' => 'clients'],
            ['name' => 'create_clients', 'description' => 'Create client profiles', 'module' => 'clients'],
            ['name' => 'edit_clients', 'description' => 'Edit client profiles', 'module' => 'clients'],
            ['name' => 'delete_clients', 'description' => 'Delete client profiles', 'module' => 'clients'],

            // Document permissions
            ['name' => 'view_documents', 'description' => 'View documents', 'module' => 'documents'],
            ['name' => 'upload_documents', 'description' => 'Upload documents', 'module' => 'documents'],
            ['name' => 'delete_documents', 'description' => 'Delete documents', 'module' => 'documents'],

            // Billing permissions
            ['name' => 'view_billing', 'description' => 'View billing information', 'module' => 'billing'],
            ['name' => 'create_invoices', 'description' => 'Create invoices', 'module' => 'billing'],
            ['name' => 'edit_invoices', 'description' => 'Edit invoices', 'module' => 'billing'],

            // Task permissions
            ['name' => 'view_tasks', 'description' => 'View tasks', 'module' => 'tasks'],
            ['name' => 'create_tasks', 'description' => 'Create tasks', 'module' => 'tasks'],
            ['name' => 'edit_tasks', 'description' => 'Edit tasks', 'module' => 'tasks'],
            ['name' => 'assign_tasks', 'description' => 'Assign tasks to staff', 'module' => 'tasks'],

            // Report permissions
            ['name' => 'view_reports', 'description' => 'View reports', 'module' => 'reports'],
            ['name' => 'export_reports', 'description' => 'Export reports', 'module' => 'reports'],

            // Admin permissions
            ['name' => 'manage_users', 'description' => 'Manage user accounts', 'module' => 'admin'],
            ['name' => 'manage_roles', 'description' => 'Manage roles and permissions', 'module' => 'admin'],
            ['name' => 'system_settings', 'description' => 'Access system settings', 'module' => 'admin'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create roles
        $roles = [
            [
                'name' => 'admin',
                'description' => 'System administrator with full access',
                'is_default' => false,
            ],
            [
                'name' => 'attorney',
                'description' => 'Attorney with case management access',
                'is_default' => false,
            ],
            [
                'name' => 'paralegal',
                'description' => 'Paralegal with limited case access',
                'is_default' => false,
            ],
            [
                'name' => 'client',
                'description' => 'Client with portal access',
                'is_default' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::create($roleData);

            // Assign permissions based on role
            switch ($role->name) {
                case 'admin':
                    $role->permissions()->attach(Permission::all());
                    break;

                case 'attorney':
                    $attorneyPermissions = [
                        'view_cases', 'create_cases', 'edit_cases', 'assign_cases',
                        'view_clients', 'create_clients', 'edit_clients',
                        'view_documents', 'upload_documents',
                        'view_billing', 'create_invoices', 'edit_invoices',
                        'view_tasks', 'create_tasks', 'edit_tasks', 'assign_tasks',
                        'view_reports',
                    ];
                    $role->permissions()->attach(
                        Permission::whereIn('name', $attorneyPermissions)->get()
                    );
                    break;

                case 'paralegal':
                    $paralegalPermissions = [
                        'view_cases', 'edit_cases',
                        'view_clients',
                        'view_documents', 'upload_documents',
                        'view_billing',
                        'view_tasks', 'edit_tasks',
                    ];
                    $role->permissions()->attach(
                        Permission::whereIn('name', $paralegalPermissions)->get()
                    );
                    break;

                case 'client':
                    $clientPermissions = [
                        'view_cases',
                        'view_documents',
                        'view_billing',
                    ];
                    $role->permissions()->attach(
                        Permission::whereIn('name', $clientPermissions)->get()
                    );
                    break;
            }
        }
    }
}
