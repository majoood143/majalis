<?php

namespace Database\Seeders;

use App\Models\ExtraService;
use App\Models\Hall;
use Illuminate\Database\Seeder;

class ExtraServiceSeeder extends Seeder
{
    public function run(): void
    {
        $halls = Hall::all();

        if ($halls->isEmpty()) {
            $this->command->warn('No halls found. Run HallSeeder first.');
            return;
        }

        $servicesTemplate = [
            [
                'name' => ['en' => 'Catering Service - Premium', 'ar' => 'خدمة تقديم طعام - فاخر'],
                'description' => [
                    'en' => 'Full catering service with international and local cuisine options',
                    'ar' => 'خدمة تقديم طعام كاملة مع خيارات مأكولات دولية ومحلية'
                ],
                'price' => 15.000,
                'unit' => 'per_person',
                'minimum_quantity' => 50,
                'maximum_quantity' => null,
                'order' => 1,
            ],
            [
                'name' => ['en' => 'Catering Service - Standard', 'ar' => 'خدمة تقديم طعام - عادي'],
                'description' => [
                    'en' => 'Standard catering service with local cuisine',
                    'ar' => 'خدمة تقديم طعام عادية مع مأكولات محلية'
                ],
                'price' => 10.000,
                'unit' => 'per_person',
                'minimum_quantity' => 50,
                'maximum_quantity' => null,
                'order' => 2,
            ],
            [
                'name' => ['en' => 'Professional Photography', 'ar' => 'تصوير احترافي'],
                'description' => [
                    'en' => 'Professional photographer with edited photos delivered within 7 days',
                    'ar' => 'مصور محترف مع صور معدلة يتم تسليمها خلال 7 أيام'
                ],
                'price' => 150.000,
                'unit' => 'fixed',
                'minimum_quantity' => 1,
                'maximum_quantity' => 1,
                'order' => 3,
            ],
            [
                'name' => ['en' => 'Videography Service', 'ar' => 'خدمة تصوير فيديو'],
                'description' => [
                    'en' => 'Professional videography with cinematic editing',
                    'ar' => 'تصوير فيديو احترافي مع مونتاج سينمائي'
                ],
                'price' => 200.000,
                'unit' => 'fixed',
                'minimum_quantity' => 1,
                'maximum_quantity' => 1,
                'order' => 4,
            ],
            [
                'name' => ['en' => 'Floral Decoration', 'ar' => 'زينة الورود'],
                'description' => [
                    'en' => 'Beautiful floral arrangements and decorations',
                    'ar' => 'تنسيقات وزينة ورود جميلة'
                ],
                'price' => 250.000,
                'unit' => 'fixed',
                'minimum_quantity' => 1,
                'maximum_quantity' => 1,
                'order' => 5,
            ],
            [
                'name' => ['en' => 'Stage Decoration', 'ar' => 'تزيين المنصة'],
                'description' => [
                    'en' => 'Custom stage decoration with lights and backdrop',
                    'ar' => 'تزيين منصة مخصص مع إضاءة وخلفية'
                ],
                'price' => 300.000,
                'unit' => 'fixed',
                'minimum_quantity' => 1,
                'maximum_quantity' => 1,
                'order' => 6,
            ],
            [
                'name' => ['en' => 'DJ Service', 'ar' => 'خدمة دي جي'],
                'description' => [
                    'en' => 'Professional DJ with sound system',
                    'ar' => 'دي جي محترف مع نظام صوت'
                ],
                'price' => 180.000,
                'unit' => 'fixed',
                'minimum_quantity' => 1,
                'maximum_quantity' => 1,
                'order' => 7,
            ],
            [
                'name' => ['en' => 'Live Band', 'ar' => 'فرقة موسيقية حية'],
                'description' => [
                    'en' => 'Live music band performance',
                    'ar' => 'عرض فرقة موسيقية حية'
                ],
                'price' => 400.000,
                'unit' => 'fixed',
                'minimum_quantity' => 1,
                'maximum_quantity' => 1,
                'order' => 8,
            ],
            [
                'name' => ['en' => 'Valet Parking', 'ar' => 'خدمة صف السيارات'],
                'description' => [
                    'en' => 'Professional valet parking service',
                    'ar' => 'خدمة صف سيارات احترافية'
                ],
                'price' => 100.000,
                'unit' => 'fixed',
                'minimum_quantity' => 1,
                'maximum_quantity' => 1,
                'order' => 9,
            ],
            [
                'name' => ['en' => 'Security Service', 'ar' => 'خدمة أمن'],
                'description' => [
                    'en' => 'Professional security personnel',
                    'ar' => 'أفراد أمن محترفون'
                ],
                'price' => 80.000,
                'unit' => 'per_hour',
                'minimum_quantity' => 4,
                'maximum_quantity' => 12,
                'order' => 10,
            ],
            [
                'name' => ['en' => 'Wedding Cake', 'ar' => 'كيك زفاف'],
                'description' => [
                    'en' => 'Custom wedding cake designed to your preferences',
                    'ar' => 'كيك زفاف مخصص حسب تفضيلاتك'
                ],
                'price' => 120.000,
                'unit' => 'fixed',
                'minimum_quantity' => 1,
                'maximum_quantity' => 1,
                'order' => 11,
            ],
            [
                'name' => ['en' => 'Chocolate Fountain', 'ar' => 'نافورة شوكولاتة'],
                'description' => [
                    'en' => 'Chocolate fountain with various dipping options',
                    'ar' => 'نافورة شوكولاتة مع خيارات غمس متنوعة'
                ],
                'price' => 50.000,
                'unit' => 'fixed',
                'minimum_quantity' => 1,
                'maximum_quantity' => 1,
                'order' => 12,
            ],
        ];

        $totalCreated = 0;

        foreach ($halls as $hall) {
            // Randomly select 5-10 services for each hall
            $numServices = rand(5, 10);
            $selectedServices = collect($servicesTemplate)->random($numServices);

            foreach ($selectedServices as $service) {
                $service['hall_id'] = $hall->id;
                $service['is_active'] = true;
                ExtraService::create($service);
                $totalCreated++;
            }
        }

        $this->command->info("✅ Created {$totalCreated} extra services across " . $halls->count() . " halls");
    }
}
