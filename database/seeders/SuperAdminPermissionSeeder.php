<?php
// database/seeders/SuperAdminPermissionSeeder.php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Clear cache ────────────────────────────────────────────
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Find Admin with ID 1 ───────────────────────────────────
        $admin = Admin::find(1);

        if (!$admin) {
            $this->command->error('❌ Admin with ID 1 not found!');
            return;
        }

        $this->command->info("Found admin: {$admin->name} ({$admin->email})");

        // ── Ensure super_admin role exists ─────────────────────────
        $superAdminRole = Role::firstOrCreate(
            [
                'name'       => 'super_admin',
                'guard_name' => 'admin',
            ],
            [
                'display_name' => 'Super Admin',
                'description'  => 'Full system access. Can access all organisations.',
                'is_system'    => true,
                'color'        => '#dc3545',
                'icon'         => 'fas fa-crown',
            ]
        );

        // ── Assign super_admin role ────────────────────────────────
        $admin->syncRoles(['super_admin']);
        $this->command->info("✅ Role assigned: super_admin");

        // ── Assign ALL permissions directly to this admin ──────────
        $allPermissions = Permission::where('guard_name', 'admin')->get();

        if ($allPermissions->isEmpty()) {
            $this->command->error('❌ No permissions found! Run RolesAndPermissionsSeeder first.');
            return;
        }

        $admin->syncPermissions($allPermissions);
        $this->command->info("✅ All {$allPermissions->count()} permissions assigned.");

        // ── Remove any organisation assignments for super_admin ────
        // Super admin does NOT need org assignment — handled via Gate::before
        $admin->organisations()->detach();
        $this->command->info("✅ Removed all organisation assignments (not needed for super_admin).");

        // ── Clear permission cache ─────────────────────────────────
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Print Summary ──────────────────────────────────────────
        $this->command->newLine();
        $this->command->info('══════════════════════════════════════════');
        $this->command->info("  Admin     : {$admin->name}");
        $this->command->info("  Email     : {$admin->email}");
        $this->command->info("  Role      : super_admin");
        $this->command->info("  Perms     : {$allPermissions->count()} (all)");
        $this->command->info("  Org Access: ALL (via Gate::before bypass)");
        $this->command->info('══════════════════════════════════════════');
        $this->command->newLine();
    }
}
