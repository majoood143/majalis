<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CommissionType;
use App\Models\ServiceFeeSetting;
use Illuminate\Database\Seeder;

/**
 * Seeder: Default Service Fee Settings
 *
 * Creates sample service fee configurations.
 * Run: php artisan db:seed --class=ServiceFeeSettingSeeder
 *
 * NOTE: Service fees are OPTIONAL. Unlike commission (which always applies),
 * service fees only apply when a setting exists. If you don't want a global
 * service fee, simply delete this seeder record.
 */
class ServiceFeeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ── Example: Global Service Fee (5%) ──
        // Remove or deactivate if you don't want a global customer fee.
        ServiceFeeSetting::create([
            'hall_id'        => null,
            'owner_id'       => null,
            'fee_type'       => CommissionType::PERCENTAGE,
            'fee_value'      => 5.00,
            'name'           => [
                'en' => 'Platform Service Fee',
                'ar' => 'رسوم خدمة المنصة',
            ],
            'description'    => [
                'en' => 'Service fee charged to customers for using the platform',
                'ar' => 'رسوم خدمة تُفرض على العملاء مقابل استخدام المنصة',
            ],
            'is_active'      => false, // ← Inactive by default — enable when ready
            'effective_from' => now()->startOfYear(),
            'effective_to'   => null,
        ]);

        $this->command->info('✅ Created global service fee setting (5% — inactive by default)');
        $this->command->warn('   → Activate via Admin Panel > Financial > Service Fees when ready.');
    }
}
