<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionCitySeeder extends Seeder
{
    public function run(): void
    {
        $regionsData = [
            [
                'name' => ['en' => 'Muscat', 'ar' => 'مسقط'],
                'code' => 'MCT',
                'latitude' => 23.5880,
                'longitude' => 58.3829,
                'order' => 1,
                'cities' => [
                    ['name' => ['en' => 'Muscat', 'ar' => 'مسقط'], 'code' => 'MCT-01', 'latitude' => 23.5880, 'longitude' => 58.3829],
                    ['name' => ['en' => 'Mutrah', 'ar' => 'مطرح'], 'code' => 'MCT-02', 'latitude' => 23.6200, 'longitude' => 58.5650],
                    ['name' => ['en' => 'Bawshar', 'ar' => 'بوشر'], 'code' => 'MCT-03', 'latitude' => 23.5773, 'longitude' => 58.3995],
                    ['name' => ['en' => 'Al Seeb', 'ar' => 'السيب'], 'code' => 'MCT-04', 'latitude' => 23.6701, 'longitude' => 58.1893],
                    ['name' => ['en' => 'Al Amerat', 'ar' => 'العامرات'], 'code' => 'MCT-05', 'latitude' => 23.4167, 'longitude' => 58.5833],
                    ['name' => ['en' => 'Quriyat', 'ar' => 'قريات'], 'code' => 'MCT-06', 'latitude' => 23.2667, 'longitude' => 58.9667],
                ]
            ],
            [
                'name' => ['en' => 'Dhofar', 'ar' => 'ظفار'],
                'code' => 'DHA',
                'latitude' => 17.0150,
                'longitude' => 54.0924,
                'order' => 2,
                'cities' => [
                    ['name' => ['en' => 'Salalah', 'ar' => 'صلالة'], 'code' => 'DHA-01', 'latitude' => 17.0150, 'longitude' => 54.0924],
                    ['name' => ['en' => 'Taqah', 'ar' => 'طاقة'], 'code' => 'DHA-02', 'latitude' => 17.0411, 'longitude' => 54.4028],
                    ['name' => ['en' => 'Mirbat', 'ar' => 'مرباط'], 'code' => 'DHA-03', 'latitude' => 16.9944, 'longitude' => 54.6967],
                    ['name' => ['en' => 'Thumrait', 'ar' => 'ثمريت'], 'code' => 'DHA-04', 'latitude' => 17.6667, 'longitude' => 54.0333],
                    ['name' => ['en' => 'Sadah', 'ar' => 'سدح'], 'code' => 'DHA-05', 'latitude' => 16.7333, 'longitude' => 53.8333],
                ]
            ],
            [
                'name' => ['en' => 'Musandam', 'ar' => 'مسندم'],
                'code' => 'MUS',
                'latitude' => 26.1847,
                'longitude' => 56.2553,
                'order' => 3,
                'cities' => [
                    ['name' => ['en' => 'Khasab', 'ar' => 'خصب'], 'code' => 'MUS-01', 'latitude' => 26.1847, 'longitude' => 56.2553],
                    ['name' => ['en' => 'Bukha', 'ar' => 'بخاء'], 'code' => 'MUS-02', 'latitude' => 25.7167, 'longitude' => 56.0833],
                    ['name' => ['en' => 'Dibba', 'ar' => 'دبا'], 'code' => 'MUS-03', 'latitude' => 25.6167, 'longitude' => 56.2667],
                    ['name' => ['en' => 'Madha', 'ar' => 'مدحاء'], 'code' => 'MUS-04', 'latitude' => 25.2833, 'longitude' => 56.2667],
                ]
            ],
            [
                'name' => ['en' => 'Al Buraimi', 'ar' => 'البريمي'],
                'code' => 'BUR',
                'latitude' => 24.2508,
                'longitude' => 55.7931,
                'order' => 4,
                'cities' => [
                    ['name' => ['en' => 'Al Buraimi', 'ar' => 'البريمي'], 'code' => 'BUR-01', 'latitude' => 24.2508, 'longitude' => 55.7931],
                    ['name' => ['en' => 'Mahdah', 'ar' => 'محضة'], 'code' => 'BUR-02', 'latitude' => 24.1500, 'longitude' => 56.3333],
                    ['name' => ['en' => 'Al Sunainah', 'ar' => 'السنينة'], 'code' => 'BUR-03', 'latitude' => 24.3667, 'longitude' => 55.9167],
                ]
            ],
            [
                'name' => ['en' => 'Ad Dakhiliyah', 'ar' => 'الداخلية'],
                'code' => 'DAK',
                'latitude' => 22.9167,
                'longitude' => 57.5333,
                'order' => 5,
                'cities' => [
                    ['name' => ['en' => 'Nizwa', 'ar' => 'نزوى'], 'code' => 'DAK-01', 'latitude' => 22.9333, 'longitude' => 57.5333],
                    ['name' => ['en' => 'Bahla', 'ar' => 'بهلاء'], 'code' => 'DAK-02', 'latitude' => 22.9667, 'longitude' => 57.3000],
                    ['name' => ['en' => 'Manah', 'ar' => 'منح'], 'code' => 'DAK-03', 'latitude' => 22.8833, 'longitude' => 57.3833],
                    ['name' => ['en' => 'Izki', 'ar' => 'إزكي'], 'code' => 'DAK-04', 'latitude' => 22.9333, 'longitude' => 57.7667],
                    ['name' => ['en' => 'Samail', 'ar' => 'سمائل'], 'code' => 'DAK-05', 'latitude' => 23.3000, 'longitude' => 57.9833],
                    ['name' => ['en' => 'Adam', 'ar' => 'أدم'], 'code' => 'DAK-06', 'latitude' => 22.3833, 'longitude' => 57.8167],
                ]
            ],
            [
                'name' => ['en' => 'Ad Dhahirah', 'ar' => 'الظاهرة'],
                'code' => 'DHA2',
                'latitude' => 23.2167,
                'longitude' => 56.7167,
                'order' => 6,
                'cities' => [
                    ['name' => ['en' => 'Ibri', 'ar' => 'عبري'], 'code' => 'DHA2-01', 'latitude' => 23.2167, 'longitude' => 56.5167],
                    ['name' => ['en' => 'Yanqul', 'ar' => 'ينقل'], 'code' => 'DHA2-02', 'latitude' => 23.5833, 'longitude' => 56.5500],
                    ['name' => ['en' => 'Dank', 'ar' => 'ضنك'], 'code' => 'DHA2-03', 'latitude' => 23.6667, 'longitude' => 57.7833],
                ]
            ],
            [
                'name' => ['en' => 'Ash Sharqiyah North', 'ar' => 'الشرقية شمال'],
                'code' => 'SHN',
                'latitude' => 22.5833,
                'longitude' => 58.5833,
                'order' => 7,
                'cities' => [
                    ['name' => ['en' => 'Ibra', 'ar' => 'إبراء'], 'code' => 'SHN-01', 'latitude' => 22.6833, 'longitude' => 58.5333],
                    ['name' => ['en' => 'Al Mudaybi', 'ar' => 'المضيبي'], 'code' => 'SHN-02', 'latitude' => 22.6167, 'longitude' => 58.7667],
                    ['name' => ['en' => 'Al Qabil', 'ar' => 'القابل'], 'code' => 'SHN-03', 'latitude' => 22.2500, 'longitude' => 58.7333],
                    ['name' => ['en' => 'Wadi Bani Khalid', 'ar' => 'وادي بني خالد'], 'code' => 'SHN-04', 'latitude' => 22.6167, 'longitude' => 59.0000],
                    ['name' => ['en' => 'Dima Wa Tayin', 'ar' => 'دماء والطائيين'], 'code' => 'SHN-05', 'latitude' => 22.3833, 'longitude' => 58.9667],
                ]
            ],
            [
                'name' => ['en' => 'Ash Sharqiyah South', 'ar' => 'الشرقية جنوب'],
                'code' => 'SHS',
                'latitude' => 21.5833,
                'longitude' => 58.9167,
                'order' => 8,
                'cities' => [
                    ['name' => ['en' => 'Sur', 'ar' => 'صور'], 'code' => 'SHS-01', 'latitude' => 22.5667, 'longitude' => 59.5289],
                    ['name' => ['en' => 'Al Kamil Wa Al Wafi', 'ar' => 'الكامل والوافي'], 'code' => 'SHS-02', 'latitude' => 22.1500, 'longitude' => 59.2833],
                    ['name' => ['en' => 'Jalan Bani Bu Ali', 'ar' => 'جعلان بني بو علي'], 'code' => 'SHS-03', 'latitude' => 21.8833, 'longitude' => 59.0000],
                    ['name' => ['en' => 'Jalan Bani Bu Hassan', 'ar' => 'جعلان بني بو حسن'], 'code' => 'SHS-04', 'latitude' => 21.9833, 'longitude' => 59.1500],
                    ['name' => ['en' => 'Masirah', 'ar' => 'مصيرة'], 'code' => 'SHS-05', 'latitude' => 20.6667, 'longitude' => 58.8833],
                ]
            ],
            [
                'name' => ['en' => 'Al Batinah North', 'ar' => 'الباطنة شمال'],
                'code' => 'BTN',
                'latitude' => 24.3667,
                'longitude' => 56.7167,
                'order' => 9,
                'cities' => [
                    ['name' => ['en' => 'Sohar', 'ar' => 'صحار'], 'code' => 'BTN-01', 'latitude' => 24.3474, 'longitude' => 56.7333],
                    ['name' => ['en' => 'Shinas', 'ar' => 'شناص'], 'code' => 'BTN-02', 'latitude' => 24.7500, 'longitude' => 56.4667],
                    ['name' => ['en' => 'Liwa', 'ar' => 'لوى'], 'code' => 'BTN-03', 'latitude' => 23.9833, 'longitude' => 57.0333],
                    ['name' => ['en' => 'Saham', 'ar' => 'صحم'], 'code' => 'BTN-04', 'latitude' => 24.1833, 'longitude' => 56.8833],
                    ['name' => ['en' => 'Al Khaburah', 'ar' => 'الخابورة'], 'code' => 'BTN-05', 'latitude' => 23.9667, 'longitude' => 57.0833],
                    ['name' => ['en' => 'Al Suwaiq', 'ar' => 'السويق'], 'code' => 'BTN-06', 'latitude' => 23.8500, 'longitude' => 57.4333],
                ]
            ],
            [
                'name' => ['en' => 'Al Batinah South', 'ar' => 'الباطنة جنوب'],
                'code' => 'BTS',
                'latitude' => 23.6833,
                'longitude' => 57.8500,
                'order' => 10,
                'cities' => [
                    ['name' => ['en' => 'Rustaq', 'ar' => 'الرستاق'], 'code' => 'BTS-01', 'latitude' => 23.3833, 'longitude' => 57.4167],
                    ['name' => ['en' => 'Al Awabi', 'ar' => 'العوابي'], 'code' => 'BTS-02', 'latitude' => 23.2833, 'longitude' => 57.5167],
                    ['name' => ['en' => 'Nakhal', 'ar' => 'نخل'], 'code' => 'BTS-03', 'latitude' => 23.3833, 'longitude' => 57.8167],
                    ['name' => ['en' => 'Wadi Al Maawil', 'ar' => 'وادي المعاول'], 'code' => 'BTS-04', 'latitude' => 23.2333, 'longitude' => 57.8000],
                    ['name' => ['en' => 'Barka', 'ar' => 'بركاء'], 'code' => 'BTS-05', 'latitude' => 23.6833, 'longitude' => 57.8833],
                    ['name' => ['en' => 'Al Musanaah', 'ar' => 'المصنعة'], 'code' => 'BTS-06', 'latitude' => 23.7667, 'longitude' => 57.8333],
                ]
            ],
            [
                'name' => ['en' => 'Al Wusta', 'ar' => 'الوسطى'],
                'code' => 'WUS',
                'latitude' => 19.5000,
                'longitude' => 56.2667,
                'order' => 11,
                'cities' => [
                    ['name' => ['en' => 'Haima', 'ar' => 'هيماء'], 'code' => 'WUS-01', 'latitude' => 19.5833, 'longitude' => 56.2833],
                    ['name' => ['en' => 'Mahout', 'ar' => 'محوت'], 'code' => 'WUS-02', 'latitude' => 18.9333, 'longitude' => 56.1500],
                    ['name' => ['en' => 'Duqm', 'ar' => 'الدقم'], 'code' => 'WUS-03', 'latitude' => 19.6667, 'longitude' => 57.7167],
                    ['name' => ['en' => 'Al Jazir', 'ar' => 'الجازر'], 'code' => 'WUS-04', 'latitude' => 19.0667, 'longitude' => 56.6167],
                ]
            ],
        ];

        foreach ($regionsData as $regionData) {
            $cities = $regionData['cities'];
            unset($regionData['cities']);

            $region = Region::create($regionData);

            foreach ($cities as $index => $cityData) {
                $cityData['region_id'] = $region->id;
                $cityData['order'] = $index + 1;
                City::create($cityData);
            }
        }

        $this->command->info('✅ Created ' . Region::count() . ' regions and ' . City::count() . ' cities');
    }
}
