<?php

namespace Database\Seeders;

use App\Models\HallFeature;
use Illuminate\Database\Seeder;

class HallFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            [
                'name' => ['en' => 'Air Conditioning', 'ar' => 'تكييف هواء'],
                'icon' => 'heroicon-o-sun',
                'description' => ['en' => 'Central air conditioning system', 'ar' => 'نظام تكييف مركزي'],
                'order' => 1,
            ],
            [
                'name' => ['en' => 'Parking', 'ar' => 'موقف سيارات'],
                'icon' => 'heroicon-o-square-3-stack-3d',
                'description' => ['en' => 'On-site parking available', 'ar' => 'موقف سيارات متوفر'],
                'order' => 2,
            ],
            [
                'name' => ['en' => 'Catering Kitchen', 'ar' => 'مطبخ تجهيز'],
                'icon' => 'heroicon-o-home',
                'description' => ['en' => 'Fully equipped catering kitchen', 'ar' => 'مطبخ تجهيز كامل المعدات'],
                'order' => 3,
            ],
            [
                'name' => ['en' => 'Sound System', 'ar' => 'نظام صوت'],
                'icon' => 'heroicon-o-speaker-wave',
                'description' => ['en' => 'Professional sound system', 'ar' => 'نظام صوت احترافي'],
                'order' => 4,
            ],
            [
                'name' => ['en' => 'Stage', 'ar' => 'منصة'],
                'icon' => 'heroicon-o-rectangle-stack',
                'description' => ['en' => 'Stage for performances', 'ar' => 'منصة للعروض'],
                'order' => 5,
            ],
            [
                'name' => ['en' => 'WiFi', 'ar' => 'واي فاي'],
                'icon' => 'heroicon-o-wifi',
                'description' => ['en' => 'High-speed internet connection', 'ar' => 'إنترنت عالي السرعة'],
                'order' => 6,
            ],
            [
                'name' => ['en' => 'Projector & Screen', 'ar' => 'بروجكتر وشاشة'],
                'icon' => 'heroicon-o-tv',
                'description' => ['en' => 'Projector and projection screen', 'ar' => 'بروجكتر وشاشة عرض'],
                'order' => 7,
            ],
            [
                'name' => ['en' => 'Tables & Chairs', 'ar' => 'طاولات وكراسي'],
                'icon' => 'heroicon-o-squares-2x2',
                'description' => ['en' => 'Tables and chairs included', 'ar' => 'طاولات وكراسي متضمنة'],
                'order' => 8,
            ],
            [
                'name' => ['en' => 'Separate Ladies Section', 'ar' => 'قسم نساء منفصل'],
                'icon' => 'heroicon-o-user-group',
                'description' => ['en' => 'Separate section for ladies', 'ar' => 'قسم منفصل للنساء'],
                'order' => 9,
            ],
            [
                'name' => ['en' => 'Prayer Room', 'ar' => 'مصلى'],
                'icon' => 'heroicon-o-building-library',
                'description' => ['en' => 'Prayer room available', 'ar' => 'مصلى متوفر'],
                'order' => 10,
            ],
            [
                'name' => ['en' => 'Restrooms', 'ar' => 'دورات مياه'],
                'icon' => 'heroicon-o-home-modern',
                'description' => ['en' => 'Clean restroom facilities', 'ar' => 'دورات مياه نظيفة'],
                'order' => 11,
            ],
            [
                'name' => ['en' => 'Wheelchair Accessible', 'ar' => 'مهيأ للكراسي المتحركة'],
                'icon' => 'heroicon-o-check-circle',
                'description' => ['en' => 'Wheelchair accessible facilities', 'ar' => 'مرافق مهيأة للكراسي المتحركة'],
                'order' => 12,
            ],
            [
                'name' => ['en' => 'Outdoor Space', 'ar' => 'مساحة خارجية'],
                'icon' => 'heroicon-o-building-office-2',
                'description' => ['en' => 'Outdoor area available', 'ar' => 'مساحة خارجية متاحة'],
                'order' => 13,
            ],
            [
                'name' => ['en' => 'Decoration Service', 'ar' => 'خدمة تزيين'],
                'icon' => 'heroicon-o-sparkles',
                'description' => ['en' => 'Professional decoration available', 'ar' => 'تزيين احترافي متاح'],
                'order' => 14,
            ],
            [
                'name' => ['en' => 'Security', 'ar' => 'حراسة أمنية'],
                'icon' => 'heroicon-o-shield-check',
                'description' => ['en' => 'Security service available', 'ar' => 'خدمة حراسة أمنية متوفرة'],
                'order' => 15,
            ],
            [
                'name' => ['en' => 'Generator Backup', 'ar' => 'مولد كهرباء احتياطي'],
                'icon' => 'heroicon-o-bolt',
                'description' => ['en' => 'Backup power generator', 'ar' => 'مولد كهرباء احتياطي'],
                'order' => 16,
            ],
            [
                'name' => ['en' => 'VIP Section', 'ar' => 'قسم VIP'],
                'icon' => 'heroicon-o-star',
                'description' => ['en' => 'VIP section available', 'ar' => 'قسم VIP متاح'],
                'order' => 17,
            ],
            [
                'name' => ['en' => 'Kids Play Area', 'ar' => 'منطقة ألعاب أطفال'],
                'icon' => 'heroicon-o-puzzle-piece',
                'description' => ['en' => 'Play area for children', 'ar' => 'منطقة ألعاب للأطفال'],
                'order' => 18,
            ],
            [
                'name' => ['en' => 'Photography Service', 'ar' => 'خدمة تصوير'],
                'icon' => 'heroicon-o-camera',
                'description' => ['en' => 'Professional photography service', 'ar' => 'خدمة تصوير احترافية'],
                'order' => 19,
            ],
            [
                'name' => ['en' => 'Buffet Setup', 'ar' => 'بوفيه مفتوح'],
                'icon' => 'heroicon-o-squares-plus',
                'description' => ['en' => 'Buffet arrangement available', 'ar' => 'ترتيب بوفيه متاح'],
                'order' => 20,
            ],
        ];

        foreach ($features as $feature) {
            HallFeature::create($feature);
        }

        $this->command->info('✅ Created ' . count($features) . ' hall features');
    }
}
