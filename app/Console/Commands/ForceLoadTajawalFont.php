<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Dompdf\Dompdf;
use Dompdf\Options;

class ForceLoadTajawalFont extends Command
{
    protected $signature = 'pdf:force-load-tajawal';
    protected $description = 'Force load Tajawal font for dompdf';

    public function handle()
    {
        $fontDir = storage_path('fonts');

        // Clear existing font metrics
        $installedFonts = $fontDir . '/installed-fonts.json';
        if (file_exists($installedFonts)) {
            unlink($installedFonts);
            $this->info('Removed old font cache');
        }

        // Remove old .ufm and .afm files
        foreach (glob($fontDir . '/*.{ufm,afm}', GLOB_BRACE) as $file) {
            unlink($file);
            $this->info('Removed: ' . basename($file));
        }

        $options = new Options();
        $options->set('fontDir', $fontDir);
        $options->set('fontCache', $fontDir);
        $options->set('defaultFont', 'Tajawal');
        $options->set('isFontSubsettingEnabled', true);

        $dompdf = new Dompdf($options);
        $fontMetrics = $dompdf->getFontMetrics();

        // Font mapping with correct weights
        $fonts = [
            ['file' => 'Tajawal-ExtraLight.ttf', 'weight' => 200, 'style' => 'normal'],
            ['file' => 'Tajawal-Light.ttf', 'weight' => 300, 'style' => 'normal'],
            ['file' => 'Tajawal-Regular.ttf', 'weight' => 400, 'style' => 'normal'],
            ['file' => 'Tajawal-Medium.ttf', 'weight' => 500, 'style' => 'normal'],
            ['file' => 'Tajawal-Bold.ttf', 'weight' => 700, 'style' => 'normal'],
            ['file' => 'Tajawal-ExtraBold.ttf', 'weight' => 800, 'style' => 'normal'],
            ['file' => 'Tajawal-Black.ttf', 'weight' => 900, 'style' => 'normal'],
        ];

        foreach ($fonts as $font) {
            $fontPath = $fontDir . '/' . $font['file'];

            if (!file_exists($fontPath)) {
                $this->error('Font not found: ' . $font['file']);
                continue;
            }

            try {
                $fontMetrics->registerFont(
                    [
                        'family' => 'Tajawal',
                        'weight' => $font['weight'],
                        'style' => $font['style']
                    ],
                    $fontPath
                );

                $this->info("✓ Loaded: Tajawal (weight: {$font['weight']})");
            } catch (\Exception $e) {
                $this->error("Failed to load {$font['file']}: " . $e->getMessage());
            }
        }

        // Verify installation
        if (file_exists($installedFonts)) {
            $this->info("\n✓ Font cache created successfully");
            $this->info("Location: {$installedFonts}");
        }

        return Command::SUCCESS;
    }
}
