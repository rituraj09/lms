<?php

namespace Database\Seeders;

// database/seeders/RolesAndPermissionsSeeder.php


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;


class RolesPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Step 1: Clear cache ────────────────────────────────────
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Step 2: Define guard ───────────────────────────────────
        $guard = 'admin';

        // ── Step 3: Create all permissions ────────────────────────
        $permissions = [
            // Organisation
            ['name' => 'organisation.view',     'display_name' => 'View Organisations',     'group' => 'Organisation',        'sort_order' => 1],
            ['name' => 'organisation.create',   'display_name' => 'Create Organisation',    'group' => 'Organisation',        'sort_order' => 2],
            ['name' => 'organisation.edit',     'display_name' => 'Edit Organisation',      'group' => 'Organisation',        'sort_order' => 3],
            ['name' => 'organisation.delete',   'display_name' => 'Delete Organisation',    'group' => 'Organisation',        'sort_order' => 4],
            ['name' => 'organisation.settings', 'display_name' => 'Manage Org Settings',    'group' => 'Organisation',        'sort_order' => 5],

            // Students
            ['name' => 'student.view',          'display_name' => 'View Students',          'group' => 'Students',            'sort_order' => 1],
            ['name' => 'student.create',        'display_name' => 'Create Student',         'group' => 'Students',            'sort_order' => 2],
            ['name' => 'student.edit',          'display_name' => 'Edit Student',           'group' => 'Students',            'sort_order' => 3],
            ['name' => 'student.delete',        'display_name' => 'Delete Student',         'group' => 'Students',            'sort_order' => 4],
            ['name' => 'student.transfer',      'display_name' => 'Transfer Student',       'group' => 'Students',            'sort_order' => 5],
            ['name' => 'student.import',        'display_name' => 'Import Students',        'group' => 'Students',            'sort_order' => 6],
            ['name' => 'student.export',        'display_name' => 'Export Students',        'group' => 'Students',            'sort_order' => 7],

            // Courses
            ['name' => 'course.view',           'display_name' => 'View Courses',           'group' => 'Courses',             'sort_order' => 1],
            ['name' => 'course.create',         'display_name' => 'Create Course',          'group' => 'Courses',             'sort_order' => 2],
            ['name' => 'course.edit',           'display_name' => 'Edit Course',            'group' => 'Courses',             'sort_order' => 3],
            ['name' => 'course.delete',         'display_name' => 'Delete Course',          'group' => 'Courses',             'sort_order' => 4],
            ['name' => 'course.assign',         'display_name' => 'Assign Course',          'group' => 'Courses',             'sort_order' => 5],
            ['name' => 'course.publish',        'display_name' => 'Publish Course',         'group' => 'Courses',             'sort_order' => 6],

            // Assessments
            ['name' => 'assessment.view',       'display_name' => 'View Assessments',       'group' => 'Assessments',         'sort_order' => 1],
            ['name' => 'assessment.create',     'display_name' => 'Create Assessment',      'group' => 'Assessments',         'sort_order' => 2],
            ['name' => 'assessment.edit',       'display_name' => 'Edit Assessment',        'group' => 'Assessments',         'sort_order' => 3],
            ['name' => 'assessment.delete',     'display_name' => 'Delete Assessment',      'group' => 'Assessments',         'sort_order' => 4],
            ['name' => 'assessment.assign',     'display_name' => 'Assign Assessment',      'group' => 'Assessments',         'sort_order' => 5],
            ['name' => 'assessment.publish',    'display_name' => 'Publish Assessment',     'group' => 'Assessments',         'sort_order' => 6],

            // Question Banks
            ['name' => 'question.view',         'display_name' => 'View Question Banks',    'group' => 'Question Banks',      'sort_order' => 1],
            ['name' => 'question.create',       'display_name' => 'Create Question',        'group' => 'Question Banks',      'sort_order' => 2],
            ['name' => 'question.edit',         'display_name' => 'Edit Question',          'group' => 'Question Banks',      'sort_order' => 3],
            ['name' => 'question.delete',       'display_name' => 'Delete Question',        'group' => 'Question Banks',      'sort_order' => 4],

            // Admin Management
            ['name' => 'admin.view',            'display_name' => 'View Admins',            'group' => 'Admin Management',    'sort_order' => 1],
            ['name' => 'admin.create',          'display_name' => 'Create Admin',           'group' => 'Admin Management',    'sort_order' => 2],
            ['name' => 'admin.edit',            'display_name' => 'Edit Admin',             'group' => 'Admin Management',    'sort_order' => 3],
            ['name' => 'admin.delete',          'display_name' => 'Delete Admin',           'group' => 'Admin Management',    'sort_order' => 4],

            // Roles & Permissions
            ['name' => 'role.view',             'display_name' => 'View Roles',             'group' => 'Roles & Permissions', 'sort_order' => 1],
            ['name' => 'role.create',           'display_name' => 'Create Role',            'group' => 'Roles & Permissions', 'sort_order' => 2],
            ['name' => 'role.edit',             'display_name' => 'Edit Role',              'group' => 'Roles & Permissions', 'sort_order' => 3],
            ['name' => 'role.delete',           'display_name' => 'Delete Role',            'group' => 'Roles & Permissions', 'sort_order' => 4],
            ['name' => 'permission.manage',     'display_name' => 'Manage User Permissions','group' => 'Roles & Permissions', 'sort_order' => 5],

            // Reports
            ['name' => 'report.view',           'display_name' => 'View Reports',           'group' => 'Reports',             'sort_order' => 1],
            ['name' => 'report.export',         'display_name' => 'Export Reports',         'group' => 'Reports',             'sort_order' => 2],

            // Activity
            ['name' => 'activity.view',         'display_name' => 'View Activity Logs',     'group' => 'Activity',            'sort_order' => 1],

            // Settings
            ['name' => 'settings.view',         'display_name' => 'View Settings',          'group' => 'Settings',            'sort_order' => 1],
            ['name' => 'settings.edit',         'display_name' => 'Edit Settings',          'group' => 'Settings',            'sort_order' => 2],

            // Leaderboard
            ['name' => 'leaderboard.view',      'display_name' => 'View Leaderboard',       'group' => 'Leaderboard',         'sort_order' => 1],
        ];

        // ── Use Spatie base models directly to avoid any custom model issues
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                [
                    'name'       => $perm['name'],
                    'guard_name' => $guard,
                ],
                [
                    'display_name' => $perm['display_name'],
                    'group'        => $perm['group'],
                    'sort_order'   => $perm['sort_order'],
                    'guard_name'   => $guard,
                ]
            );
        }

        // ── Clear cache again after creating permissions ───────────
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Step 4: Create Roles ───────────────────────────────────

        // Super Admin
        $superAdmin = Role::firstOrCreate(
            [
                'name'       => 'super_admin',
                'guard_name' => $guard,
            ],
            [
                'display_name' => 'Super Admin',
                'description'  => 'Full system access.',
                'is_system'    => true,
                'color'        => '#dc3545',
                'icon'         => 'ri ri-vip-crown-line',
                'guard_name'   => $guard,
            ]
        );
        $superAdmin->syncPermissions([
            'organisation.view', 'organisation.edit', 'organisation.settings',
            'student.view', 'student.create', 'student.edit',
            'student.transfer', 'student.import', 'student.export',
            'course.view', 'course.assign',
            'assessment.view', 'assessment.assign',
            'question.view',  'question.create', 'question.edit',
            'admin.view','admin.create','admin.edit','admin.delete',
            'report.view', 'report.export',
            'activity.view',
            'settings.view',
            'leaderboard.view',
        ]);
        // Admin
        $adminRole = Role::firstOrCreate(
            [
                'name'       => 'admin',
                'guard_name' => $guard,
            ],
            [
                'display_name' => 'Admin',
                'description'  => 'General administrator.',
                'is_system'    => true,
                'color'        => '#0d6efd',
                'icon'         => 'ri ri-user-settings-fill',
                'guard_name'   => $guard,
            ]
        );
        $adminRole->syncPermissions([
            'organisation.view', 'organisation.edit', 'organisation.settings',
            'student.view', 'student.create', 'student.edit',
            'student.transfer', 'student.import', 'student.export',
            'course.view', 'course.assign',
            'assessment.view', 'assessment.assign',
            'question.view',
            'admin.view',
            'report.view', 'report.export',
            'activity.view',
            'settings.view',
            'leaderboard.view',
        ]);

        // Master Trainer
        $trainerRole = Role::firstOrCreate(
            [
                'name'       => 'master_trainer',
                'guard_name' => $guard,
            ],
            [
                'display_name' => 'Master Trainer',
                'description'  => 'Trainer with student & assignment access.',
                'is_system'    => true,
                'color'        => '#198754',
                'icon'         => 'ri ri-presentation-fill',
                'guard_name'   => $guard,
            ]
        );
        $trainerRole->syncPermissions([
            'organisation.view',
            'student.view', 'student.create', 'student.edit',
            'student.import', 'student.export',
            'report.view',
        ]);

        // ── Final cache clear ──────────────────────────────────────
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✅ Roles and Permissions seeded successfully.');
    }
}
