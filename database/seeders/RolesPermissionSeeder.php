<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'admin';

        // ══════════════════════════════════════════════════════════════
        // PART 1: CREATE ALL PERMISSIONS
        // ══════════════════════════════════════════════════════════════

        $permissions = [

            // ────────────────────────────────────────────────────────
            // SYSTEM MODE PERMISSIONS (system.*)
            // ────────────────────────────────────────────────────────

            // System - Dashboard
            ['name' => 'system.dashboard.view',              'display_name' => 'View System Dashboard',           'group' => 'System - Dashboard',        'sort_order' => 1],

            // System - Courses
            ['name' => 'system.course.view',                 'display_name' => 'View All Courses',                'group' => 'System - Courses',          'sort_order' => 1],
            ['name' => 'system.course.create',               'display_name' => 'Create Course',                   'group' => 'System - Courses',          'sort_order' => 2],
            ['name' => 'system.course.edit',                 'display_name' => 'Edit Course',                     'group' => 'System - Courses',          'sort_order' => 3],
            ['name' => 'system.course.delete',               'display_name' => 'Delete Course',                   'group' => 'System - Courses',          'sort_order' => 4],
            ['name' => 'system.course.publish',              'display_name' => 'Publish Course',                  'group' => 'System - Courses',          'sort_order' => 5],

            // System - Modules
            ['name' => 'system.module.view',                 'display_name' => 'View Modules',                    'group' => 'System - Modules',          'sort_order' => 1],
            ['name' => 'system.module.create',               'display_name' => 'Create Module',                   'group' => 'System - Modules',          'sort_order' => 2],
            ['name' => 'system.module.edit',                 'display_name' => 'Edit Module',                     'group' => 'System - Modules',          'sort_order' => 3],
            ['name' => 'system.module.delete',               'display_name' => 'Delete Module',                   'group' => 'System - Modules',          'sort_order' => 4],

            // System - Enrollments
            ['name' => 'system.enrollment.view',             'display_name' => 'View Enrollments',                'group' => 'System - Enrollments',      'sort_order' => 1],
            ['name' => 'system.enrollment.create',           'display_name' => 'Create Enrollment',               'group' => 'System - Enrollments',      'sort_order' => 2],
            ['name' => 'system.enrollment.delete',           'display_name' => 'Delete Enrollment',               'group' => 'System - Enrollments',      'sort_order' => 3],

            // System - Questions
            ['name' => 'system.question.view',               'display_name' => 'View Question Banks',             'group' => 'System - Questions',        'sort_order' => 1],
            ['name' => 'system.question.create',             'display_name' => 'Create Question',                 'group' => 'System - Questions',        'sort_order' => 2],
            ['name' => 'system.question.edit',               'display_name' => 'Edit Question',                   'group' => 'System - Questions',        'sort_order' => 3],
            ['name' => 'system.question.delete',             'display_name' => 'Delete Question',                 'group' => 'System - Questions',        'sort_order' => 4],

            // System - Assessments
            ['name' => 'system.assessment.view',             'display_name' => 'View Assessments',                'group' => 'System - Assessments',      'sort_order' => 1],
            ['name' => 'system.assessment.create',           'display_name' => 'Create Assessment',               'group' => 'System - Assessments',      'sort_order' => 2],
            ['name' => 'system.assessment.edit',             'display_name' => 'Edit Assessment',                 'group' => 'System - Assessments',      'sort_order' => 3],
            ['name' => 'system.assessment.delete',           'display_name' => 'Delete Assessment',               'group' => 'System - Assessments',      'sort_order' => 4],
            ['name' => 'system.assessment.publish',          'display_name' => 'Publish Assessment',              'group' => 'System - Assessments',      'sort_order' => 5],

            // System - Students
            ['name' => 'system.student.view',                'display_name' => 'View All Students',               'group' => 'System - Students',         'sort_order' => 1],
            ['name' => 'system.student.create',              'display_name' => 'Create Student',                  'group' => 'System - Students',         'sort_order' => 2],
            ['name' => 'system.student.edit',                'display_name' => 'Edit Student',                    'group' => 'System - Students',         'sort_order' => 3],
            ['name' => 'system.student.delete',              'display_name' => 'Delete Student',                  'group' => 'System - Students',         'sort_order' => 4],
            ['name' => 'system.student.import',              'display_name' => 'Import Students',                 'group' => 'System - Students',         'sort_order' => 5],
            ['name' => 'system.student.export',              'display_name' => 'Export Students',                 'group' => 'System - Students',         'sort_order' => 6],

            // System - Administrators
            ['name' => 'system.admin.view',                  'display_name' => 'View Administrators',             'group' => 'System - Administrators',   'sort_order' => 1],
            ['name' => 'system.admin.create',                'display_name' => 'Create Administrator',            'group' => 'System - Administrators',   'sort_order' => 2],
            ['name' => 'system.admin.edit',                  'display_name' => 'Edit Administrator',              'group' => 'System - Administrators',   'sort_order' => 3],
            ['name' => 'system.admin.delete',                'display_name' => 'Delete Administrator',            'group' => 'System - Administrators',   'sort_order' => 4],
            ['name' => 'system.admin.assign_org',            'display_name' => 'Assign Organisation to Admin',    'group' => 'System - Administrators',   'sort_order' => 5],
            ['name' => 'system.admin.assign_permissions',    'display_name' => 'Manage Admin Permissions',        'group' => 'System - Administrators',   'sort_order' => 6],
            ['name' => 'system.admin.view_super_admin',      'display_name' => 'View Super Admin (Protected)',    'group' => 'System - Administrators',   'sort_order' => 7],

            // System - Roles
            ['name' => 'system.role.view',                   'display_name' => 'View Roles',                      'group' => 'System - Roles',            'sort_order' => 1],
            ['name' => 'system.role.create',                 'display_name' => 'Create Role',                     'group' => 'System - Roles',            'sort_order' => 2],
            ['name' => 'system.role.edit',                   'display_name' => 'Edit Role',                       'group' => 'System - Roles',            'sort_order' => 3],
            ['name' => 'system.role.delete',                 'display_name' => 'Delete Role',                     'group' => 'System - Roles',            'sort_order' => 4],

            // System - Organisations
            ['name' => 'system.organisation.view',           'display_name' => 'View All Organisations',          'group' => 'System - Organisations',    'sort_order' => 1],
            ['name' => 'system.organisation.create',         'display_name' => 'Create Organisation',             'group' => 'System - Organisations',    'sort_order' => 2],
            ['name' => 'system.organisation.edit',           'display_name' => 'Edit Organisation',               'group' => 'System - Organisations',    'sort_order' => 3],
            ['name' => 'system.organisation.delete',         'display_name' => 'Delete Organisation',             'group' => 'System - Organisations',    'sort_order' => 4],
            ['name' => 'system.organisation.settings',       'display_name' => 'Manage Org Settings',             'group' => 'System - Organisations',    'sort_order' => 5],

            // System - Reports
            ['name' => 'system.report.view',                 'display_name' => 'View System Reports',             'group' => 'System - Reports',          'sort_order' => 1],
            ['name' => 'system.report.export',               'display_name' => 'Export System Reports',           'group' => 'System - Reports',          'sort_order' => 2],

            // System - Communication
            ['name' => 'system.communication.view',          'display_name' => 'View Communications',             'group' => 'System - Communication',    'sort_order' => 1],
            ['name' => 'system.communication.send',          'display_name' => 'Send Communications',             'group' => 'System - Communication',    'sort_order' => 2],

            // System - Settings
            ['name' => 'system.settings.view',               'display_name' => 'View System Settings',            'group' => 'System - Settings',         'sort_order' => 1],
            ['name' => 'system.settings.edit',               'display_name' => 'Edit System Settings',            'group' => 'System - Settings',         'sort_order' => 2],

            // System - Activity
            ['name' => 'system.activity.view',               'display_name' => 'View Activity Logs',              'group' => 'System - Activity',         'sort_order' => 1],


            // ────────────────────────────────────────────────────────
            // ORGANISATION MODE PERMISSIONS (org.*)
            // ────────────────────────────────────────────────────────

            // Org - Dashboard
            ['name' => 'org.dashboard.view',                 'display_name' => 'View Org Dashboard',              'group' => 'Org - Dashboard',           'sort_order' => 1],

            // Org - Navigation
            ['name' => 'org.home.access',                    'display_name' => 'Access Org Home',                 'group' => 'Org - Navigation',          'sort_order' => 1],

            // Org - Students
            ['name' => 'org.student.view',                   'display_name' => 'View Org Students',               'group' => 'Org - Students',            'sort_order' => 1],
            ['name' => 'org.student.create',                 'display_name' => 'Add Student',                     'group' => 'Org - Students',            'sort_order' => 2],
            ['name' => 'org.student.edit',                   'display_name' => 'Edit Student',                    'group' => 'Org - Students',            'sort_order' => 3],
            ['name' => 'org.student.delete',                 'display_name' => 'Delete Student',                  'group' => 'Org - Students',            'sort_order' => 4],
            ['name' => 'org.student.import',                 'display_name' => 'Import Students',                 'group' => 'Org - Students',            'sort_order' => 5],
            ['name' => 'org.student.export',                 'display_name' => 'Export Students',                 'group' => 'Org - Students',            'sort_order' => 6],
            ['name' => 'org.student.transfer',               'display_name' => 'Transfer Student',                'group' => 'Org - Students',            'sort_order' => 7],

            // Org - Courses
            ['name' => 'org.course.view',                    'display_name' => 'View Assigned Courses',           'group' => 'Org - Courses',             'sort_order' => 1],
            ['name' => 'org.course.assign',                  'display_name' => 'Assign Course to Students',       'group' => 'Org - Courses',             'sort_order' => 2],

            // Org - Assessments
            ['name' => 'org.assessment.view',                'display_name' => 'View Assigned Assessments',       'group' => 'Org - Assessments',         'sort_order' => 1],
            ['name' => 'org.assessment.assign',              'display_name' => 'Assign Assessment to Students',   'group' => 'Org - Assessments',         'sort_order' => 2],

            // Org - Reports
            ['name' => 'org.report.view',                    'display_name' => 'View Org Reports',                'group' => 'Org - Reports',             'sort_order' => 1],
            ['name' => 'org.report.export',                  'display_name' => 'Export Org Reports',              'group' => 'Org - Reports',             'sort_order' => 2],

            // Org - Activity
            ['name' => 'org.activity.view',                  'display_name' => 'View Org Activity',               'group' => 'Org - Activity',            'sort_order' => 1],

            // Org - Settings
            ['name' => 'org.settings.view',                  'display_name' => 'View Org Settings',               'group' => 'Org - Settings',            'sort_order' => 1],
            ['name' => 'org.settings.edit',                  'display_name' => 'Edit Org Settings',               'group' => 'Org - Settings',            'sort_order' => 2],

            // Org - Leaderboard
            ['name' => 'org.leaderboard.view',               'display_name' => 'View Leaderboard',                'group' => 'Org - Leaderboard',         'sort_order' => 1],

        ];

        // ── Create all permissions ─────────────────────────────────
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => $guard],
                [
                    'display_name' => $perm['display_name'],
                    'group'        => $perm['group'],
                    'sort_order'   => $perm['sort_order'],
                ]
            );
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ══════════════════════════════════════════════════════════════
        // PART 2: CREATE ROLES (Designations ONLY - No Auto Permissions)
        // ══════════════════════════════════════════════════════════════

        // ── SUPER ADMIN ────────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => $guard],
            [
                'display_name' => 'Super Admin',
                'description'  => 'System owner - full access to everything',
                'is_system'    => true,
                'color'        => '#dc3545',
                'icon'         => 'ri ri-vip-crown-line',
            ]
        );

        // ✅ Super admin gets ALL permissions automatically
        $allPermissions = Permission::where('guard_name', $guard)->pluck('name')->toArray();
        $superAdmin->syncPermissions($allPermissions);

        // ── ADMIN (Designation ONLY) ────────────────────────────────
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => $guard],
            [
                'display_name' => 'Admin',
                'description'  => 'Administrator - permissions assigned individually',
                'is_system'    => false,
                'color'        => '#0d6efd',
                'icon'         => 'ri ri-user-settings-fill',
            ]
        );
        // ❌ NO auto permissions - assigned per user

        // ── MASTER TRAINER (Designation ONLY) ───────────────────────
        $trainerRole = Role::firstOrCreate(
            ['name' => 'master_trainer', 'guard_name' => $guard],
            [
                'display_name' => 'Master Trainer',
                'description'  => 'Trainer - permissions assigned individually',
                'is_system'    => false,
                'color'        => '#198754',
                'icon'         => 'ri ri-presentation-fill',
            ]
        );
        // ❌ NO auto permissions - assigned per user

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✅ Roles (designations) and permissions created successfully.');
        $this->command->info('ℹ️  Only super_admin has auto permissions. Others get custom assignments.');
    }
}
