<?php
// create file: public/load-tajawal.php

require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$fontDir = __DIR__ . '/../storage/fonts';

$fonts = [
    'Tajawal-ExtraLight.ttf' => ['family' => 'Tajawal', 'weight' => 200],
    'Tajawal-Light.ttf' => ['family' => 'Tajawal', 'weight' => 300],
    'Tajawal-Regular.ttf' => ['family' => 'Tajawal', 'weight' => 400],
    'Tajawal-Medium.ttf' => ['family' => 'Tajawal', 'weight' => 500],
    'Tajawal-Bold.ttf' => ['family' => 'Tajawal', 'weight' => 700],
    'Tajawal-ExtraBold.ttf' => ['family' => 'Tajawal', 'weight' => 800],
    'Tajawal-Black.ttf' => ['family' => 'Tajawal', 'weight' => 900],
];

$options = new Options();
$options->set('fontDir', $fontDir);
$options->set('fontCache', $fontDir);
$options->set('defaultFont', 'Tajawal');

$dompdf = new Dompdf($options);
$fontMetrics = $dompdf->getFontMetrics();

foreach ($fonts as $file => $info) {
    $filePath = $fontDir . '/' . $file;
    if (file_exists($filePath)) {
        echo "Loading: $file\n";

        $fontMetrics->registerFont(
            [
                'family' => $info['family'],
                'weight' => $info['weight'],
                'style' => 'normal'
            ],
            $filePath
        );
        echo "✓ Loaded: {$info['family']} weight {$info['weight']}\n";
    } else {
        echo "✗ File not found: $file\n";
    }
}

echo "\nFont loading complete!\n";
echo "Check " . $fontDir . "/installed-fonts.json\n";
