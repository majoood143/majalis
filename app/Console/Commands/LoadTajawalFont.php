<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Dompdf\Dompdf;
use Dompdf\Options;

class LoadTajawalFont extends Command
{
    protected $signature = 'pdf:load-tajawal-font';
    protected $description = 'Load Tajawal Arabic font for dompdf';

    public function handle()
    {
        $fontDir = storage_path('fonts');

        // Map Tajawal font weights
        $fonts = [
            'Tajawal-ExtraLight' => ['weight' => 200, 'style' => 'normal'],
            'Tajawal-Light' => ['weight' => 300, 'style' => 'normal'],
            'Tajawal-Regular' => ['weight' => 400, 'style' => 'normal'],
            'Tajawal-Medium' => ['weight' => 500, 'style' => 'normal'],
            'Tajawal-Bold' => ['weight' => 700, 'style' => 'normal'],
            'Tajawal-ExtraBold' => ['weight' => 800, 'style' => 'normal'],
            'Tajawal-Black' => ['weight' => 900, 'style' => 'normal'],
        ];

        $dompdf = new Dompdf();
        $fontMetrics = $dompdf->getFontMetrics();

        foreach ($fonts as $fontName => $properties) {
            $fontFile = $fontDir . '/' . $fontName . '.ttf';

            if (!file_exists($fontFile)) {
                $this->warn("Font file not found: {$fontFile}");
                continue;
            }

            // Register the font
            $fontMetrics->registerFont(
                [
                    'family' => 'Tajawal',
                    'weight' => $properties['weight'],
                    'style' => $properties['style']
                ],
                $fontFile
            );

            $this->info("Registered: {$fontName} (weight: {$properties['weight']})");
        }

        $this->info('Tajawal font family loaded successfully!');
    }
}
