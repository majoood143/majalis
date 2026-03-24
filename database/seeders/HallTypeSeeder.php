<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HallType;
use App\Models\HallCategory;

class HallTypeSeeder extends Seeder
{
    public function run()
    {
        // First create categories
        $socialCategory = HallCategory::create([
            'slug' => 'social',
            'name' => ['en' => 'Social Events', 'ar' => 'المناسبات الاجتماعية'],
            'sort_order' => 1,
        ]);
        
        $corporateCategory = HallCategory::create([
            'slug' => 'corporate',
            'name' => ['en' => 'Corporate Events', 'ar' => 'المناسبات الرسمية'],
            'sort_order' => 2,
        ]);
        
        $outdoorCategory = HallCategory::create([
            'slug' => 'outdoor',
            'name' => ['en' => 'Outdoor Venues', 'ar' => 'الأماكن الخارجية'],
            'sort_order' => 3,
        ]);
        
        // Create hall types
        $hallTypes = [
            [
                'slug' => 'conference',
                'name' => ['en' => 'Conference Hall', 'ar' => 'قاعة المؤتمرات'],
                'description' => ['en' => 'Large hall for conferences and presentations', 'ar' => 'قاعة كبيرة للمؤتمرات والعروض التقديمية'],
                'icon' => 'heroicon-o-presentation-chart-line',
                'color' => 'primary',
                'category_id' => $corporateCategory->id,
                'sort_order' => 1,
            ],
            [
                'slug' => 'banquet',
                'name' => ['en' => 'Banquet Hall', 'ar' => 'قاعة الولائم'],
                'description' => ['en' => 'Perfect for weddings and formal dinners', 'ar' => 'مثالية لحفلات الزفاف والعشاء الرسمي'],
                'icon' => 'heroicon-o-cake',
                'color' => 'success',
                'category_id' => $socialCategory->id,
                'sort_order' => 1,
            ],
            [
                'slug' => 'meeting',
                'name' => ['en' => 'Meeting Room', 'ar' => 'غرفة الاجتماعات'],
                'description' => ['en' => 'Small to medium space for business meetings', 'ar' => 'مساحة صغيرة إلى متوسطة لاجتماعات العمل'],
                'icon' => 'heroicon-o-users',
                'color' => 'info',
                'category_id' => $corporateCategory->id,
                'sort_order' => 2,
            ],
            [
                'slug' => 'ballroom',
                'name' => ['en' => 'Ballroom', 'ar' => 'قاعة المناسبات الكبرى'],
                'description' => ['en' => 'Grand space for galas and large events', 'ar' => 'مساحة فخمة للمناسبات الكبرى والحفلات'],
                'icon' => 'heroicon-o-star',
                'color' => 'warning',
                'category_id' => $socialCategory->id,
                'sort_order' => 2,
            ],
            [
                'slug' => 'outdoor',
                'name' => ['en' => 'Outdoor Space', 'ar' => 'المسرح الخارجي'],
                'description' => ['en' => 'Open air venue with natural setting', 'ar' => 'مكان مفتوح في الهواء الطلق'],
                'icon' => 'heroicon-o-sun',
                'color' => 'success',
                'category_id' => $outdoorCategory->id,
                'sort_order' => 1,
            ],
            // Add more types as needed
        ];
        
        foreach ($hallTypes as $type) {
            HallType::create($type);
        }
    }
}