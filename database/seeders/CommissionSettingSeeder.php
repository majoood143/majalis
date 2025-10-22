<?php

namespace Database\Seeders;

use App\Enums\CommissionType;
use App\Models\CommissionSetting;
use Illuminate\Database\Seeder;

class CommissionSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Global default commission (10%)
        CommissionSetting::create([
            'hall_id' => null,
            'owner_id' => null,
            'commission_type' => CommissionType::PERCENTAGE,
            'commission_value' => 10.00,
            'name' => [
                'en' => 'Global Platform Commission',
                'ar' => 'عمولة المنصة العامة'
            ],
            'description' => [
                'en' => 'Default platform commission for all bookings',
                'ar' => 'عمولة المنصة الافتراضية لجميع الحجوزات'
            ],
            'is_active' => true,
            'effective_from' => now()->startOfYear(),
            'effective_to' => null,
        ]);

        $this->command->info('✅ Created global commission setting (10%)');
    }
}
