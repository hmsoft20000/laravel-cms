<?php

namespace HMsoft\Cms\Database\Seeders;

use HMsoft\Cms\Models\Permission;
use HMsoft\Cms\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorizationSeeder extends Seeder
{
    /**
     * Store created roles for parent assignment
     */
    private array $createdRoles = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $this->createPermissions();

        // Create roles
        $this->createRoles();

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        // Create a super admin user if none exists
        $this->createSuperAdminUser();
    }

    /**
     * Create all system permissions
     */
    private function createPermissions(): void
    {
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'users', 'description' => 'Can view user list'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'users', 'description' => 'Can create new users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'module' => 'users', 'description' => 'Can edit existing users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'users', 'description' => 'Can delete users'],
            ['name' => 'Manage User Roles', 'slug' => 'users.manage-roles', 'module' => 'users', 'description' => 'Can assign/remove user roles'],

            // Posts/Content Management
            ['name' => 'View Posts', 'slug' => 'posts.view', 'module' => 'posts', 'description' => 'Can view all posts'],
            ['name' => 'Create Posts', 'slug' => 'posts.create', 'module' => 'posts', 'description' => 'Can create new posts'],
            ['name' => 'Edit Posts', 'slug' => 'posts.edit', 'module' => 'posts', 'description' => 'Can edit existing posts'],
            ['name' => 'Delete Posts', 'slug' => 'posts.delete', 'module' => 'posts', 'description' => 'Can delete posts'],
            ['name' => 'Publish Posts', 'slug' => 'posts.publish', 'module' => 'posts', 'description' => 'Can publish/unpublish posts'],
            ['name' => 'Manage Post Media', 'slug' => 'posts.manage-media', 'module' => 'posts', 'description' => 'Can manage post media files'],

            // Categories Management
            ['name' => 'View Categories', 'slug' => 'categories.view', 'module' => 'categories', 'description' => 'Can view categories'],
            ['name' => 'Create Categories', 'slug' => 'categories.create', 'module' => 'categories', 'description' => 'Can create categories'],
            ['name' => 'Edit Categories', 'slug' => 'categories.edit', 'module' => 'categories', 'description' => 'Can edit categories'],
            ['name' => 'Delete Categories', 'slug' => 'categories.delete', 'module' => 'categories', 'description' => 'Can delete categories'],
            ['name' => 'Publish Categories', 'slug' => 'categories.publish', 'module' => 'categories', 'description' => 'Can publish/unpublish categories'],

            // Attributes Management
            ['name' => 'View Attributes', 'slug' => 'attributes.view', 'module' => 'attributes', 'description' => 'Can view attributes'],
            ['name' => 'Create Attributes', 'slug' => 'attributes.create', 'module' => 'attributes', 'description' => 'Can create attributes'],
            ['name' => 'Edit Attributes', 'slug' => 'attributes.edit', 'module' => 'attributes', 'description' => 'Can edit attributes'],
            ['name' => 'Delete Attributes', 'slug' => 'attributes.delete', 'module' => 'attributes', 'description' => 'Can delete attributes'],
            ['name' => 'Publish Attributes', 'slug' => 'attributes.publish', 'module' => 'attributes', 'description' => 'Can publish/unpublish attributes'],

            // Features Management
            ['name' => 'View Features', 'slug' => 'features.view', 'module' => 'features', 'description' => 'Can view features'],
            ['name' => 'Create Features', 'slug' => 'features.create', 'module' => 'features', 'description' => 'Can create features'],
            ['name' => 'Edit Features', 'slug' => 'features.edit', 'module' => 'features', 'description' => 'Can edit features'],
            ['name' => 'Delete Features', 'slug' => 'features.delete', 'module' => 'features', 'description' => 'Can delete features'],
            ['name' => 'Publish Features', 'slug' => 'features.publish', 'module' => 'features', 'description' => 'Can publish/unpublish features'],

            // FAQ Management
            ['name' => 'View FAQs', 'slug' => 'faqs.view', 'module' => 'faqs', 'description' => 'Can view FAQs'],
            ['name' => 'Create FAQs', 'slug' => 'faqs.create', 'module' => 'faqs', 'description' => 'Can create FAQs'],
            ['name' => 'Edit FAQs', 'slug' => 'faqs.edit', 'module' => 'faqs', 'description' => 'Can edit FAQs'],
            ['name' => 'Delete FAQs', 'slug' => 'faqs.delete', 'module' => 'faqs', 'description' => 'Can delete FAQs'],
            ['name' => 'Publish FAQs', 'slug' => 'faqs.publish', 'module' => 'faqs', 'description' => 'Can publish/unpublish FAQs'],

            // Organizations Management
            ['name' => 'View Organizations', 'slug' => 'organizations.view', 'module' => 'organizations', 'description' => 'Can view organizations'],
            ['name' => 'Create Organizations', 'slug' => 'organizations.create', 'module' => 'organizations', 'description' => 'Can create organizations'],
            ['name' => 'Edit Organizations', 'slug' => 'organizations.edit', 'module' => 'organizations', 'description' => 'Can edit organizations'],
            ['name' => 'Delete Organizations', 'slug' => 'organizations.delete', 'module' => 'organizations', 'description' => 'Can delete organizations'],
            ['name' => 'Publish Organizations', 'slug' => 'organizations.publish', 'module' => 'organizations', 'description' => 'Can publish/unpublish organizations'],

            // Plans Management
            ['name' => 'View Plans', 'slug' => 'plans.view', 'module' => 'plans', 'description' => 'Can view plans'],
            ['name' => 'Create Plans', 'slug' => 'plans.create', 'module' => 'plans', 'description' => 'Can create plans'],
            ['name' => 'Edit Plans', 'slug' => 'plans.edit', 'module' => 'plans', 'description' => 'Can edit plans'],
            ['name' => 'Delete Plans', 'slug' => 'plans.delete', 'module' => 'plans', 'description' => 'Can delete plans'],
            ['name' => 'Publish Plans', 'slug' => 'plans.publish', 'module' => 'plans', 'description' => 'Can publish/unpublish plans'],

            // Sectors Management
            ['name' => 'View Sectors', 'slug' => 'sectors.view', 'module' => 'sectors', 'description' => 'Can view sectors'],
            ['name' => 'Create Sectors', 'slug' => 'sectors.create', 'module' => 'sectors', 'description' => 'Can create sectors'],
            ['name' => 'Edit Sectors', 'slug' => 'sectors.edit', 'module' => 'sectors', 'description' => 'Can edit sectors'],
            ['name' => 'Delete Sectors', 'slug' => 'sectors.delete', 'module' => 'sectors', 'description' => 'Can delete sectors'],
            ['name' => 'Publish Sectors', 'slug' => 'sectors.publish', 'module' => 'sectors', 'description' => 'Can publish/unpublish sectors'],

            // Settings Management
            ['name' => 'View Settings', 'slug' => 'settings.view', 'module' => 'settings', 'description' => 'Can view system settings'],
            ['name' => 'Edit Settings', 'slug' => 'settings.edit', 'module' => 'settings', 'description' => 'Can edit system settings'],

            // Contact/Communication
            ['name' => 'View Messages', 'slug' => 'messages.view', 'module' => 'messages', 'description' => 'Can view contact messages'],
            ['name' => 'Reply Messages', 'slug' => 'messages.reply', 'module' => 'messages', 'description' => 'Can reply to messages'],
            ['name' => 'Delete Messages', 'slug' => 'messages.delete', 'module' => 'messages', 'description' => 'Can delete messages'],

            // Reports & Analytics
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'reports', 'description' => 'Can view system reports'],
            ['name' => 'Export Data', 'slug' => 'reports.export', 'module' => 'reports', 'description' => 'Can export data'],

            // Pages Management
            ['name' => 'View Pages', 'slug' => 'pages.view', 'module' => 'pages', 'description' => 'Can view page management'],
            ['name' => 'Edit Pages', 'slug' => 'pages.edit', 'module' => 'pages', 'description' => 'Can edit page content and settings'],

            // Legal Documents
            ['name' => 'View Legal Documents', 'slug' => 'legal.view', 'module' => 'legal', 'description' => 'Can view legal documents'],
            ['name' => 'Edit Legal Documents', 'slug' => 'legal.edit', 'module' => 'legal', 'description' => 'Can edit legal documents'],

            // System Administration
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'system', 'description' => 'Can create/edit/delete roles'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'module' => 'system', 'description' => 'Can manage permissions'],
            ['name' => 'System Backup', 'slug' => 'system.backup', 'module' => 'system', 'description' => 'Can perform system backups'],
            ['name' => 'View Logs', 'slug' => 'system.logs', 'module' => 'system', 'description' => 'Can view system logs'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }

    /**
     * Create system roles with hierarchical structure
     */
    private function createRoles(): void
    {
        $roles = [
            // Root Level Roles
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access with all permissions',
                'level' => 100,
                'parent_id' => null
            ],
            [
                'name' => 'System Administrator',
                'slug' => 'system-admin',
                'description' => 'System-level administrative access',
                'level' => 90,
                'parent_id' => null
            ],

            // Content Management Hierarchy
            [
                'name' => 'Content Manager',
                'slug' => 'content-manager',
                'description' => 'Manages all content-related operations',
                'level' => 70,
                'parent_id' => null
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Can manage content and posts',
                'level' => 60,
                'parent_id' => null // Will be set to content-manager after creation
            ],
            [
                'name' => 'Author',
                'slug' => 'author',
                'description' => 'Can create and edit own content',
                'level' => 40,
                'parent_id' => null // Will be set to editor after creation
            ],

            // User Management Hierarchy
            [
                'name' => 'User Manager',
                'slug' => 'user-manager',
                'description' => 'Manages user accounts and permissions',
                'level' => 65,
                'parent_id' => null
            ],
            [
                'name' => 'Moderator',
                'slug' => 'moderator',
                'description' => 'Can moderate content and users',
                'level' => 30,
                'parent_id' => null // Will be set to user-manager after creation
            ],

            // Basic Roles
            [
                'name' => 'Registered User',
                'slug' => 'user',
                'description' => 'Basic registered user with limited access',
                'level' => 10,
                'parent_id' => null
            ],
            [
                'name' => 'Guest',
                'slug' => 'guest',
                'description' => 'Unregistered visitor with minimal access',
                'level' => 1,
                'parent_id' => null
            ],
        ];

        // Create roles first
        foreach ($roles as $role) {
            $createdRole = Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );

            // Store reference for parent assignment
            $this->createdRoles[$role['slug']] = $createdRole;
        }

        // Now assign parents to create hierarchy
        $hierarchy = [
            'content-manager' => [
                'editor' => [
                    'author'
                ]
            ],
            'user-manager' => [
                'moderator'
            ]
        ];

        $this->assignParentRelationships($hierarchy);
    }

    /**
     * Assign parent-child relationships for roles
     */
    private function assignParentRelationships(array $hierarchy, $parentSlug = null): void
    {
        foreach ($hierarchy as $currentSlug => $children) {
            if (isset($this->createdRoles[$currentSlug])) {
                $currentRole = $this->createdRoles[$currentSlug];

                if ($parentSlug && isset($this->createdRoles[$parentSlug])) {
                    $currentRole->update(['parent_id' => $this->createdRoles[$parentSlug]->id]);
                }

                if (is_array($children)) {
                    $this->assignParentRelationships($children, $currentSlug);
                }
            }
        }
    }

    /**
     * Assign permissions to roles
     */
    private function assignPermissionsToRoles(): void
    {
        $rolePermissions = [
            'super-admin' => [
                // All permissions
                'users.*', 'posts.*', 'categories.*', 'attributes.*', 'features.*', 'faqs.*', 'organizations.*', 'plans.*', 'sectors.*', 'settings.*', 'messages.*', 'reports.*', 'pages.*', 'legal.*', 'roles.*', 'permissions.*', 'system.*'
            ],
            'system-admin' => [
                // System-level permissions
                'users.*', 'settings.*', 'pages.*', 'legal.*', 'roles.manage', 'permissions.manage', 'system.*', 'reports.*'
            ],
            'content-manager' => [
                // Content management permissions
                'posts.*', 'categories.*', 'attributes.*', 'features.*', 'faqs.*', 'pages.*', 'legal.*', 'messages.view', 'reports.view'
            ],
            'editor' => [
                // Editor permissions (inherits from content-manager)
                'posts.view', 'posts.create', 'posts.edit', 'posts.publish', 'posts.manage-media',
                'categories.view', 'categories.create', 'categories.edit', 'categories.publish',
                'attributes.view', 'attributes.create', 'attributes.edit', 'attributes.publish',
                'features.view', 'features.create', 'features.edit', 'features.publish',
                'faqs.view', 'faqs.create', 'faqs.edit', 'faqs.publish',
                'pages.view', 'pages.edit', 'legal.view'
            ],
            'author' => [
                // Author permissions (inherits from editor)
                'posts.view', 'posts.create', 'posts.edit', 'posts.manage-media',
                'categories.view', 'attributes.view', 'features.view', 'faqs.view', 'pages.view'
            ],
            'user-manager' => [
                // User management permissions
                'users.view', 'users.create', 'users.edit', 'users.manage-roles', 'messages.*'
            ],
            'moderator' => [
                // Moderator permissions (inherits from user-manager)
                'users.view', 'posts.view', 'posts.edit', 'messages.view', 'messages.reply'
            ],
            'user' => [
                // Basic user permissions
                'posts.view'
            ],
            'guest' => [
                // Guest user permissions (public access)
                'organizations.view',
                'posts.view',
                'categories.view',
                'sectors.view',
                'legal.view',
                'pages.view',
                'faqs.view',
                'features.view',
                'plans.view'
            ]
        ];

        foreach ($rolePermissions as $roleSlug => $permissions) {
            $role = Role::where('slug', $roleSlug)->first();
            if ($role) {
                $permissionIds = [];

                foreach ($permissions as $permission) {
                    if (str_contains($permission, '*')) {
                        // Wildcard permission - get all permissions in module
                        $module = str_replace('.*', '', $permission);
                        $modulePermissions = Permission::where('module', $module)->pluck('id')->toArray();
                        $permissionIds = array_merge($permissionIds, $modulePermissions);
                    } else {
                        // Specific permission
                        $perm = Permission::where('slug', $permission)->first();
                        if ($perm) {
                            $permissionIds[] = $perm->id;
                        }
                    }
                }

                $role->permissions()->sync($permissionIds);
            }
        }
    }

    /**
     * Create a super admin user
     */
    private function createSuperAdminUser(): void
    {
        $userModelClass = config('auth.providers.users.model', \App\Models\User::class);
        $superAdmin = $userModelClass::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => bcrypt('password'),
                'is_admin' => true, // This user will bypass all permission checks
                'is_active' => true,
            ]
        );

        // Assign super-admin role
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdmin->roles()->sync([$superAdminRole->id]);
        }
    }
}
