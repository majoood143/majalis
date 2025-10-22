<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\HallOwner;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@majalis.om',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'phone' => '99123456',
            'phone_country_code' => '+968',
            'is_active' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        $this->command->info('✅ Created Admin: admin@majalis.om / password');

        // Create Hall Owners
        $owners = [
            [
                'user' => [
                    'name' => 'Ahmed Al Lawati',
                    'email' => 'ahmed@majalis.om',
                    'password' => Hash::make('password'),
                    'role' => UserRole::HALL_OWNER,
                    'phone' => '99234567',
                    'phone_country_code' => '+968',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                ],
                'owner' => [
                    'business_name' => 'Al Lawati Events',
                    'business_name_ar' => 'مناسبات اللواتي',
                    'commercial_registration' => 'CR-2024-001',
                    'tax_number' => 'TAX-001',
                    'business_phone' => '24123456',
                    'business_email' => 'info@royaloccasions.om',
                    'business_address' => 'Muscat, Al Khuwair',
                    'business_address_ar' => 'مسقط، الخوير',
                    'bank_name' => 'National Bank of Oman',
                    'bank_account_name' => 'Royal Occasions LLC',
                    'bank_account_number' => '9876543210',
                    'iban' => 'OM34NBO9876543210987654',
                    'is_verified' => true,
                    'verified_at' => now(),
                    'is_active' => true,
                ],
            ],
            [
                'user' => [
                    'name' => 'Mohammed Al Hinai',
                    'email' => 'mohammed@majalis.om',
                    'password' => Hash::make('password'),
                    'role' => UserRole::HALL_OWNER,
                    'phone' => '99456789',
                    'phone_country_code' => '+968',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                ],
                'owner' => [
                    'business_name' => 'Grand Hall Services',
                    'business_name_ar' => 'خدمات القاعات الكبرى',
                    'commercial_registration' => 'CR-2024-003',
                    'tax_number' => 'TAX-003',
                    'business_phone' => '24345678',
                    'business_email' => 'info@grandhall.om',
                    'business_address' => 'Sohar Industrial Area',
                    'business_address_ar' => 'صحار، المنطقة الصناعية',
                    'bank_name' => 'Bank Dhofar',
                    'bank_account_name' => 'Grand Hall Services LLC',
                    'bank_account_number' => '5555666677',
                    'iban' => 'OM56BDH5555666677778888',
                    'is_verified' => true,
                    'verified_at' => now(),
                    'is_active' => true,
                ],
            ],
        ];

        foreach ($owners as $ownerData) {
            $user = User::create($ownerData['user']);

            $ownerData['owner']['user_id'] = $user->id;
            $ownerData['owner']['verified_by'] = $admin->id;

            HallOwner::create($ownerData['owner']);

            $this->command->info("✅ Created Hall Owner: {$user->email} / password");
        }

        // Create Customer Users
        $customers = [
            [
                'name' => 'Ali Al Maamari',
                'email' => 'ali@example.om',
                'password' => Hash::make('password'),
                'role' => UserRole::CUSTOMER,
                'phone' => '99567890',
                'phone_country_code' => '+968',
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'name' => 'Sara Al Rashdi',
                'email' => 'sara@example.om',
                'password' => Hash::make('password'),
                'role' => UserRole::CUSTOMER,
                'phone' => '99678901',
                'phone_country_code' => '+968',
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'name' => 'Hassan Al Habsi',
                'email' => 'hassan@example.om',
                'password' => Hash::make('password'),
                'role' => UserRole::CUSTOMER,
                'phone' => '99789012',
                'phone_country_code' => '+968',
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'name' => 'Maryam Al Wahaibi',
                'email' => 'maryam@example.om',
                'password' => Hash::make('password'),
                'role' => UserRole::CUSTOMER,
                'phone' => '99890123',
                'phone_country_code' => '+968',
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'name' => 'Khalid Al Siyabi',
                'email' => 'khalid@example.om',
                'password' => Hash::make('password'),
                'role' => UserRole::CUSTOMER,
                'phone' => '99901234',
                'phone_country_code' => '+968',
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
        ];

        foreach ($customers as $customerData) {
            $customer = User::create($customerData);
            $this->command->info("✅ Created Customer: {$customer->email} / password");
        }

        $this->command->info("\n📊 Users Summary:");
        $this->command->info("Admin: " . User::where('role', UserRole::ADMIN)->count());
        $this->command->info("Hall Owners: " . User::where('role', UserRole::HALL_OWNER)->count());
        $this->command->info("Customers: " . User::where('role', UserRole::CUSTOMER)->count());
    }
}
