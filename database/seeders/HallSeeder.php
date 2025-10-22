<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Hall;
use App\Models\HallFeature;
use App\Models\User;
use Illuminate\Database\Seeder;

class HallSeeder extends Seeder
{
    public function run(): void
    {
        $owners = User::where('role', 'hall_owner')->get();
        $features = HallFeature::pluck('id')->toArray();

        if ($owners->isEmpty()) {
            $this->command->warn('⚠️  No hall owners found. Run UserSeeder first.');
            return;
        }

        if ($owners->count() < 3) {
            $this->command->warn('⚠️  Less than 3 hall owners found. Some halls will be assigned to available owners.');
        }

        $halls = [
            // Muscat Halls
            [
                'city_id' => City::where('code', 'MCT-01')->first()?->id,
                'owner_id' => $owners->get(0)->id,
                'name' => ['en' => 'Grand Palace Hall', 'ar' => 'قاعة القصر الكبير'],
                'description' => [
                    'en' => 'Luxurious grand hall perfect for weddings and corporate events. Features elegant chandeliers, marble flooring, and state-of-the-art facilities.',
                    'ar' => 'قاعة فخمة كبيرة مثالية لحفلات الزفاف والمناسبات الشركات. تتميز بثريات أنيقة وأرضيات رخامية ومرافق حديثة.'
                ],
                'address' => 'Al Khuwair, Muscat',
                'address_localized' => ['en' => 'Al Khuwair, Muscat', 'ar' => 'الخوير، مسقط'],
                'latitude' => 23.5926,
                'longitude' => 58.4107,
                'capacity_min' => 100,
                'capacity_max' => 500,
                'price_per_slot' => 300.000,
                'pricing_override' => [
                    'morning' => 250.000,
                    'afternoon' => 300.000,
                    'evening' => 400.000,
                    'full_day' => 800.000,
                ],
                'phone' => '24123456',
                'whatsapp' => '99123456',
                'email' => 'grandpalace@majalis.om',
                'features' => array_slice($features, 0, 12),
                'is_active' => true,
                'is_featured' => true,
                'cancellation_hours' => 48,
                'cancellation_fee_percentage' => 20,
            ],
            [
                'city_id' => City::where('code', 'MCT-02')->first()?->id,
                'owner_id' => $owners->get(0)->id,
                'name' => ['en' => 'Mutrah Seaside Hall', 'ar' => 'قاعة مطرح البحرية'],
                'description' => [
                    'en' => 'Beautiful beachfront hall with stunning sea views. Perfect for beach weddings and outdoor events.',
                    'ar' => 'قاعة جميلة على الشاطئ مع إطلالات خلابة على البحر. مثالية لحفلات الزفاف الشاطئية والمناسبات الخارجية.'
                ],
                'address' => 'Mutrah Corniche, Muscat',
                'address_localized' => ['en' => 'Mutrah Corniche, Muscat', 'ar' => 'كورنيش مطرح، مسقط'],
                'latitude' => 23.6200,
                'longitude' => 58.5650,
                'capacity_min' => 50,
                'capacity_max' => 300,
                'price_per_slot' => 250.000,
                'phone' => '24123457',
                'whatsapp' => '99123457',
                'features' => array_slice($features, 0, 10),
                'is_active' => true,
                'is_featured' => true,
                'cancellation_hours' => 24,
                'cancellation_fee_percentage' => 15,
            ],
            [
                'city_id' => City::where('code', 'MCT-03')->first()?->id,
                'owner_id' => $owners->get(1) ? $owners->get(1)->id : $owners->get(0)->id,
                'name' => ['en' => 'Royal Garden Hall', 'ar' => 'قاعة الحديقة الملكية'],
                'description' => [
                    'en' => 'Elegant hall with beautiful garden space for outdoor ceremonies. Indoor and outdoor options available.',
                    'ar' => 'قاعة أنيقة مع حديقة جميلة للاحتفالات الخارجية. خيارات داخلية وخارجية متاحة.'
                ],
                'address' => 'Bawshar, Near Al Mouj',
                'address_localized' => ['en' => 'Bawshar, Near Al Mouj', 'ar' => 'بوشر، بالقرب من الموج'],
                'latitude' => 23.5773,
                'longitude' => 58.3995,
                'capacity_min' => 80,
                'capacity_max' => 400,
                'price_per_slot' => 280.000,
                'pricing_override' => [
                    'morning' => 220.000,
                    'afternoon' => 280.000,
                    'evening' => 350.000,
                    'full_day' => 700.000,
                ],
                'phone' => '24234567',
                'whatsapp' => '99234567',
                'features' => array_slice($features, 0, 14),
                'is_active' => true,
                'is_featured' => true,
                'cancellation_hours' => 48,
                'cancellation_fee_percentage' => 25,
            ],
            [
                'city_id' => City::where('code', 'MCT-04')->first()?->id,
                'owner_id' => $owners->get(1) ? $owners->get(1)->id : $owners->get(0)->id,
                'name' => ['en' => 'Seeb Convention Center', 'ar' => 'مركز السيب للمؤتمرات'],
                'description' => [
                    'en' => 'Modern convention center perfect for conferences, exhibitions, and large gatherings.',
                    'ar' => 'مركز مؤتمرات حديث مثالي للمؤتمرات والمعارض والتجمعات الكبيرة.'
                ],
                'address' => 'Al Seeb, Near Airport',
                'address_localized' => ['en' => 'Al Seeb, Near Airport', 'ar' => 'السيب، بالقرب من المطار'],
                'latitude' => 23.6701,
                'longitude' => 58.1893,
                'capacity_min' => 200,
                'capacity_max' => 1000,
                'price_per_slot' => 500.000,
                'phone' => '24345678',
                'whatsapp' => '99345678',
                'features' => array_slice($features, 0, 16),
                'is_active' => true,
                'is_featured' => false,
                'cancellation_hours' => 72,
                'cancellation_fee_percentage' => 30,
            ],
            // Salalah Halls
            [
                'city_id' => City::where('code', 'DHA-01')->first()?->id,
                'owner_id' => $owners->get(2) ? $owners->get(2)->id : $owners->get(0)->id,
                'name' => ['en' => 'Salalah Grand Ballroom', 'ar' => 'قاعة صلالة الكبرى'],
                'description' => [
                    'en' => 'Premier event venue in Salalah with traditional Omani architecture and modern amenities.',
                    'ar' => 'مكان رئيسي للمناسبات في صلالة مع عمارة عمانية تقليدية ووسائل راحة حديثة.'
                ],
                'address' => 'Salalah City Center',
                'address_localized' => ['en' => 'Salalah City Center', 'ar' => 'مركز مدينة صلالة'],
                'latitude' => 17.0150,
                'longitude' => 54.0924,
                'capacity_min' => 100,
                'capacity_max' => 600,
                'price_per_slot' => 320.000,
                'phone' => '23456789',
                'whatsapp' => '99456789',
                'features' => array_slice($features, 0, 13),
                'is_active' => true,
                'is_featured' => true,
                'cancellation_hours' => 48,
                'cancellation_fee_percentage' => 20,
            ],
            // Sohar Hall
            [
                'city_id' => City::where('code', 'BTN-01')->first()?->id,
                'owner_id' => $owners->get(2) ? $owners->get(2)->id : $owners->get(0)->id,
                'name' => ['en' => 'Sohar Pearl Hall', 'ar' => 'قاعة لؤلؤة صحار'],
                'description' => [
                    'en' => 'Elegant hall in the heart of Sohar, perfect for all types of celebrations and events.',
                    'ar' => 'قاعة أنيقة في قلب صحار، مثالية لجميع أنواع الاحتفالات والمناسبات.'
                ],
                'address' => 'Sohar City Center',
                'address_localized' => ['en' => 'Sohar City Center', 'ar' => 'مركز مدينة صحار'],
                'latitude' => 24.3474,
                'longitude' => 56.7333,
                'capacity_min' => 75,
                'capacity_max' => 350,
                'price_per_slot' => 200.000,
                'pricing_override' => [
                    'morning' => 180.000,
                    'afternoon' => 200.000,
                    'evening' => 250.000,
                    'full_day' => 550.000,
                ],
                'phone' => '26567890',
                'whatsapp' => '99567890',
                'features' => array_slice($features, 0, 11),
                'is_active' => true,
                'is_featured' => false,
                'cancellation_hours' => 24,
                'cancellation_fee_percentage' => 15,
            ],
            // Nizwa Hall
            [
                'city_id' => City::where('code', 'DAK-01')->first()?->id,
                'owner_id' => $owners->get(0)->id,
                'name' => ['en' => 'Nizwa Heritage Hall', 'ar' => 'قاعة تراث نزوى'],
                'description' => [
                    'en' => 'Traditional Omani-style hall showcasing rich heritage with modern facilities.',
                    'ar' => 'قاعة على الطراز العماني التقليدي تعرض تراثاً غنياً مع مرافق حديثة.'
                ],
                'address' => 'Nizwa, Near Nizwa Fort',
                'address_localized' => ['en' => 'Nizwa, Near Nizwa Fort', 'ar' => 'نزوى، بالقرب من قلعة نزوى'],
                'latitude' => 22.9333,
                'longitude' => 57.5333,
                'capacity_min' => 60,
                'capacity_max' => 250,
                'price_per_slot' => 180.000,
                'phone' => '25678901',
                'whatsapp' => '99678901',
                'features' => array_slice($features, 0, 9),
                'is_active' => true,
                'is_featured' => false,
                'cancellation_hours' => 24,
                'cancellation_fee_percentage' => 10,
            ],
            // Sur Hall
            [
                'city_id' => City::where('code', 'SHS-01')->first()?->id,
                'owner_id' => $owners->get(1) ? $owners->get(1)->id : $owners->get(0)->id,
                'name' => ['en' => 'Sur Coastal Hall', 'ar' => 'قاعة صور الساحلية'],
                'description' => [
                    'en' => 'Beachside venue with panoramic ocean views, perfect for destination weddings.',
                    'ar' => 'مكان على الشاطئ مع إطلالات بانورامية على المحيط، مثالي لحفلات الزفاف.'
                ],
                'address' => 'Sur Corniche',
                'address_localized' => ['en' => 'Sur Corniche', 'ar' => 'كورنيش صور'],
                'latitude' => 22.5667,
                'longitude' => 59.5289,
                'capacity_min' => 50,
                'capacity_max' => 200,
                'price_per_slot' => 150.000,
                'phone' => '25789012',
                'whatsapp' => '99789012',
                'features' => array_slice($features, 0, 8),
                'is_active' => true,
                'is_featured' => false,
                'cancellation_hours' => 24,
                'cancellation_fee_percentage' => 15,
            ],
        ];

        foreach ($halls as $hallData) {
            if (!$hallData['city_id']) {
                continue;
            }

            $hall = Hall::create($hallData);
            $this->command->info("✅ Created Hall: {$hall->name}");
        }

        $this->command->info("\n📊 Created " . Hall::count() . " halls");
    }
}
