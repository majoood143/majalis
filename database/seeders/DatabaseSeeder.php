<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Majalis Database Seeding...');
        $this->command->newLine();

        // Order is important - follow dependencies
        $seeders = [
            RegionCitySeeder::class,
            HallFeatureSeeder::class,
            UserSeeder::class,
            CommissionSettingSeeder::class,
            HallSeeder::class,
            ExtraServiceSeeder::class,
        ];

        foreach ($seeders as $seeder) {
            $this->command->info("Running: " . class_basename($seeder));
            $this->call($seeder);
            $this->command->newLine();
        }

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();
        $this->displaySummary();
    }

    private function displaySummary(): void
    {
        $this->command->info('📊 Database Summary:');
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Regions', \App\Models\Region::count()],
                ['Cities', \App\Models\City::count()],
                ['Hall Features', \App\Models\HallFeature::count()],
                ['Users', \App\Models\User::count()],
                ['  - Admins', \App\Models\User::where('role', 'admin')->count()],
                ['  - Hall Owners', \App\Models\User::where('role', 'hall_owner')->count()],
                ['  - Customers', \App\Models\User::where('role', 'customer')->count()],
                ['Hall Owners (Verified)', \App\Models\HallOwner::where('is_verified', true)->count()],
                ['Commission Settings', \App\Models\CommissionSetting::count()],
                ['Halls', \App\Models\Hall::count()],
                ['  - Active', \App\Models\Hall::where('is_active', true)->count()],
                ['  - Featured', \App\Models\Hall::where('is_featured', true)->count()],
                ['Extra Services', \App\Models\ExtraService::count()],
            ]
        );

        $this->command->newLine();
        $this->command->info('🔐 Login Credentials:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@majalis.om', 'password'],
                ['Hall Owner', 'ahmed@majalis.om', 'password'],
                ['Hall Owner', 'fatima@majalis.om', 'password'],
                ['Hall Owner', 'mohammed@majalis.om', 'password'],
                ['Customer', 'ali@example.om', 'password'],
            ]
        );
    }
}
