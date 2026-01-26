<?php

declare(strict_types=1);

/**
 * ExpenseCategorySeeder
 * 
 * Seeds the database with default system expense categories.
 * These categories are available to all hall owners.
 * 
 * @package Database\Seeders
 * @author  Majalis Development Team
 * @version 1.0.0
 */

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

/**
 * ExpenseCategorySeeder Class
 */
class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $categories = [
            // Operational Categories
            [
                'name' => ['en' => 'Electricity', 'ar' => 'كهرباء'],
                'description' => ['en' => 'Electricity bills and power costs', 'ar' => 'فواتير الكهرباء وتكاليف الطاقة'],
                'color' => '#f59e0b',
                'icon' => 'heroicon-o-bolt',
                'type' => 'utility',
                'is_system' => true,
                'order' => 1,
            ],
            [
                'name' => ['en' => 'Water', 'ar' => 'مياه'],
                'description' => ['en' => 'Water bills and water supply costs', 'ar' => 'فواتير المياه وتكاليف إمدادات المياه'],
                'color' => '#3b82f6',
                'icon' => 'heroicon-o-beaker',
                'type' => 'utility',
                'is_system' => true,
                'order' => 2,
            ],
            [
                'name' => ['en' => 'Internet & Phone', 'ar' => 'إنترنت وهاتف'],
                'description' => ['en' => 'Internet and telephone service costs', 'ar' => 'تكاليف خدمات الإنترنت والهاتف'],
                'color' => '#8b5cf6',
                'icon' => 'heroicon-o-wifi',
                'type' => 'utility',
                'is_system' => true,
                'order' => 3,
            ],
            [
                'name' => ['en' => 'Rent', 'ar' => 'إيجار'],
                'description' => ['en' => 'Monthly or yearly rent payments', 'ar' => 'مدفوعات الإيجار الشهرية أو السنوية'],
                'color' => '#ef4444',
                'icon' => 'heroicon-o-building-office',
                'type' => 'operational',
                'is_system' => true,
                'order' => 4,
            ],

            // Staff Categories
            [
                'name' => ['en' => 'Staff Salaries', 'ar' => 'رواتب الموظفين'],
                'description' => ['en' => 'Employee salaries and wages', 'ar' => 'رواتب وأجور الموظفين'],
                'color' => '#10b981',
                'icon' => 'heroicon-o-users',
                'type' => 'staff',
                'is_system' => true,
                'order' => 5,
            ],
            [
                'name' => ['en' => 'Temporary Staff', 'ar' => 'عمالة مؤقتة'],
                'description' => ['en' => 'Temporary or event-based staff costs', 'ar' => 'تكاليف العمالة المؤقتة أو الخاصة بالفعاليات'],
                'color' => '#14b8a6',
                'icon' => 'heroicon-o-user-plus',
                'type' => 'staff',
                'is_system' => true,
                'order' => 6,
            ],

            // Maintenance Categories
            [
                'name' => ['en' => 'Maintenance', 'ar' => 'صيانة'],
                'description' => ['en' => 'General maintenance and repairs', 'ar' => 'الصيانة العامة والإصلاحات'],
                'color' => '#f97316',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'type' => 'maintenance',
                'is_system' => true,
                'order' => 7,
            ],
            [
                'name' => ['en' => 'Air Conditioning', 'ar' => 'تكييف'],
                'description' => ['en' => 'AC maintenance and repairs', 'ar' => 'صيانة وإصلاح التكييف'],
                'color' => '#06b6d4',
                'icon' => 'heroicon-o-sun',
                'type' => 'maintenance',
                'is_system' => true,
                'order' => 8,
            ],
            [
                'name' => ['en' => 'Cleaning', 'ar' => 'تنظيف'],
                'description' => ['en' => 'Cleaning services and supplies', 'ar' => 'خدمات ومواد التنظيف'],
                'color' => '#22c55e',
                'icon' => 'heroicon-o-sparkles',
                'type' => 'maintenance',
                'is_system' => true,
                'order' => 9,
            ],

            // Event Categories
            [
                'name' => ['en' => 'Catering', 'ar' => 'تموين'],
                'description' => ['en' => 'Food and beverage for events', 'ar' => 'طعام وشراب للفعاليات'],
                'color' => '#ec4899',
                'icon' => 'heroicon-o-cake',
                'type' => 'event',
                'is_system' => true,
                'order' => 10,
            ],
            [
                'name' => ['en' => 'Decoration', 'ar' => 'ديكور'],
                'description' => ['en' => 'Event decoration and setup', 'ar' => 'ديكور وتجهيز الفعاليات'],
                'color' => '#a855f7',
                'icon' => 'heroicon-o-paint-brush',
                'type' => 'event',
                'is_system' => true,
                'order' => 11,
            ],
            [
                'name' => ['en' => 'Sound & Lighting', 'ar' => 'صوت وإضاءة'],
                'description' => ['en' => 'Audio and lighting equipment', 'ar' => 'معدات الصوت والإضاءة'],
                'color' => '#6366f1',
                'icon' => 'heroicon-o-speaker-wave',
                'type' => 'event',
                'is_system' => true,
                'order' => 12,
            ],
            [
                'name' => ['en' => 'Photography', 'ar' => 'تصوير'],
                'description' => ['en' => 'Photography and videography services', 'ar' => 'خدمات التصوير الفوتوغرافي والفيديو'],
                'color' => '#78716c',
                'icon' => 'heroicon-o-camera',
                'type' => 'event',
                'is_system' => true,
                'order' => 13,
            ],

            // Marketing Categories
            [
                'name' => ['en' => 'Marketing', 'ar' => 'تسويق'],
                'description' => ['en' => 'Advertising and marketing expenses', 'ar' => 'نفقات الإعلان والتسويق'],
                'color' => '#0ea5e9',
                'icon' => 'heroicon-o-megaphone',
                'type' => 'marketing',
                'is_system' => true,
                'order' => 14,
            ],
            [
                'name' => ['en' => 'Social Media', 'ar' => 'وسائل التواصل'],
                'description' => ['en' => 'Social media advertising', 'ar' => 'إعلانات وسائل التواصل الاجتماعي'],
                'color' => '#1d4ed8',
                'icon' => 'heroicon-o-share',
                'type' => 'marketing',
                'is_system' => true,
                'order' => 15,
            ],

            // Other Categories
            [
                'name' => ['en' => 'Insurance', 'ar' => 'تأمين'],
                'description' => ['en' => 'Insurance premiums and coverage', 'ar' => 'أقساط التأمين والتغطية'],
                'color' => '#64748b',
                'icon' => 'heroicon-o-shield-check',
                'type' => 'operational',
                'is_system' => true,
                'order' => 16,
            ],
            [
                'name' => ['en' => 'Equipment', 'ar' => 'معدات'],
                'description' => ['en' => 'Equipment purchase and rental', 'ar' => 'شراء وتأجير المعدات'],
                'color' => '#475569',
                'icon' => 'heroicon-o-cube',
                'type' => 'operational',
                'is_system' => true,
                'order' => 17,
            ],
            [
                'name' => ['en' => 'Supplies', 'ar' => 'مستلزمات'],
                'description' => ['en' => 'Office and hall supplies', 'ar' => 'مستلزمات المكتب والقاعة'],
                'color' => '#71717a',
                'icon' => 'heroicon-o-archive-box',
                'type' => 'operational',
                'is_system' => true,
                'order' => 18,
            ],
            [
                'name' => ['en' => 'Licenses & Permits', 'ar' => 'تراخيص وتصاريح'],
                'description' => ['en' => 'Business licenses and permits', 'ar' => 'الرخص التجارية والتصاريح'],
                'color' => '#dc2626',
                'icon' => 'heroicon-o-document-check',
                'type' => 'operational',
                'is_system' => true,
                'order' => 19,
            ],
            [
                'name' => ['en' => 'Other', 'ar' => 'أخرى'],
                'description' => ['en' => 'Miscellaneous expenses', 'ar' => 'مصروفات متنوعة'],
                'color' => '#a1a1aa',
                'icon' => 'heroicon-o-ellipsis-horizontal-circle',
                'type' => 'other',
                'is_system' => true,
                'order' => 99,
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                [
                    'is_system' => true,
                    'type' => $category['type'],
                    'order' => $category['order'],
                ],
                $category
            );
        }

        $this->command->info('Expense categories seeded successfully!');
    }
}
