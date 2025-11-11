<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

/**
 * Shield Seeder
 *
 * Creates initial roles, permissions, and super admin user.
 * This seeder should be run AFTER php artisan shield:generate --all
 *
 * Run with: php artisan db:seed --class=ShieldSeeder
 */
class ShieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method:
     * 1. Creates essential roles (super_admin, admin, hall_owner, customer)
     * 2. Creates a super admin user
     * 3. Assigns all permissions to super_admin role
     * 4. Assigns appropriate permissions to other roles
     *
     * @return void
     */
    public function run(): void
    {
        // Clear cache to avoid permission issues
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /**
         * Create essential roles for the Majalis platform
         *
         * Roles:
         * - super_admin: Full system access, bypasses all permission checks
         * - admin: Administrative access, can manage most resources
         * - hall_owner: Can manage their own halls and bookings
         * - customer: Can browse and book halls
         * - filament_user: Basic panel access (created by Shield)
         */
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['guard_name' => 'web']
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );

        $hallOwnerRole = Role::firstOrCreate(
            ['name' => 'hall_owner'],
            ['guard_name' => 'web']
        );

        $customerRole = Role::firstOrCreate(
            ['name' => 'customer'],
            ['guard_name' => 'web']
        );

        // Ensure filament_user role exists (created by Shield)
        $filamentUserRole = Role::firstOrCreate(
            ['name' => 'filament_user'],
            ['guard_name' => 'web']
        );

        /**
         * Assign all permissions to super_admin role.
         * Super admins should have unrestricted access to everything.
         */
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);

        /**
         * Assign specific permissions to admin role.
         * Admins can manage most resources but not user roles/permissions.
         */
        $adminPermissions = Permission::where('name', 'not like', '%role%')
            ->where('name', 'not like', '%permission%')
            ->get();
        $adminRole->syncPermissions($adminPermissions);

        /**
         * Assign hall-related permissions to hall_owner role.
         * Hall owners can only manage their own halls and bookings.
         */
        $hallOwnerPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', '%hall%')
                ->orWhere('name', 'like', '%booking%')
                ->orWhere('name', 'like', '%service%')
                ->orWhere('name', 'like', '%availability%');
        })->get();
        $hallOwnerRole->syncPermissions($hallOwnerPermissions);

        /**
         * Create super admin user.
         * IMPORTANT: Change these credentials in production!
         *
         * After running this seeder:
         * 1. Login with these credentials
         * 2. Immediately change the password
         * 3. Create additional admin users as needed
         */
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@majalis.om'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // CHANGE THIS IN PRODUCTION!
                'phone' => '+96812345678',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        /**
         * Assign super_admin role to the user.
         * This enables the user to access the panel and bypass all permission checks.
         */
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        // Also assign filament_user role for redundancy
        if (!$superAdmin->hasRole('filament_user')) {
            $superAdmin->assignRole('filament_user');
        }

        $this->command->info('✅ Shield seeder completed successfully!');
        $this->command->info('📧 Super Admin Email: admin@majalis.om');
        $this->command->warn('🔒 Super Admin Password: password (CHANGE THIS IN PRODUCTION!)');
        $this->command->info('🌐 Access panel at: ' . url('/admin'));
    }
}
