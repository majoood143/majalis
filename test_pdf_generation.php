<?php

declare(strict_types=1);

/**
 * Direct PDF Generation Test
 * 
 * This script attempts to generate the PDF directly and shows
 * the EXACT error message and line number.
 * 
 * Usage:
 * php test_pdf_generation.php <booking_id>
 * 
 * Example:
 * php test_pdf_generation.php 1
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Booking;
use App\Services\InvoiceService;

if (!isset($argv[1])) {
    echo "Usage: php test_pdf_generation.php <booking_id>\n";
    exit(1);
}

$bookingId = (int) $argv[1];

echo "\n🧪 Testing PDF Generation for Booking #{$bookingId}\n";
echo str_repeat("=", 80) . "\n\n";

// Load booking
echo "Loading booking...\n";
$booking = Booking::with(['hall.owner', 'hall.city.region', 'user', 'extraServices'])->find($bookingId);

if (!$booking) {
    echo "❌ Booking #{$bookingId} not found!\n";
    exit(1);
}

echo "✅ Booking loaded: {$booking->booking_number}\n";
echo "   Customer: {$booking->customer_name}\n";
echo "   Hall: " . (is_array($booking->hall->name) ? $booking->hall->name['en'] ?? 'N/A' : $booking->hall->name) . "\n\n";

// Test sanitization function
echo "Testing sanitization function...\n";
try {
    $service = new InvoiceService();
    $reflection = new ReflectionMethod($service, 'sanitizeForPdf');
    $reflection->setAccessible(true);
    
    $testString = "Test 👨‍💼 String";
    $sanitized = $reflection->invoke($service, $testString);
    
    echo "   Input:  '{$testString}'\n";
    echo "   Output: '{$sanitized}'\n";
    
    if (strpos($testString, '👨') !== false && strpos($sanitized, '👨') === false) {
        echo "   ✅ Sanitization working correctly\n\n";
    } else {
        echo "   ⚠️  Sanitization might not be working\n\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Sanitization test failed: {$e->getMessage()}\n\n";
}

// Test data preparation
echo "Preparing invoice data...\n";
try {
    $service = new InvoiceService();
    $method = new ReflectionMethod($service, 'prepareInvoiceData');
    $method->setAccessible(true);
    
    $data = $method->invoke($service, $booking, 'advance');
    
    echo "   ✅ Data prepared successfully\n";
    echo "   Customer Name: {$data['customerName']}\n";
    echo "   Hall Name: {$data['hallName']}\n";
    echo "   Event Type: {$data['eventType']}\n\n";
    
    // Check for emojis in prepared data
    $hasEmoji = false;
    foreach ($data as $key => $value) {
        if (is_string($value) && preg_match('/[\x{1F000}-\x{1F9FF}]/u', $value)) {
            echo "   ⚠️  Field '{$key}' still contains emoji: {$value}\n";
            $hasEmoji = true;
        }
    }
    
    if (!$hasEmoji) {
        echo "   ✅ No emojis in prepared data\n\n";
    }
    
} catch (\Exception $e) {
    echo "   ❌ Data preparation failed: {$e->getMessage()}\n\n";
    exit(1);
}

// Test blade rendering
echo "Testing blade template rendering...\n";
try {
    $view = view('invoices.advance-payment', $data);
    $html = $view->render();
    
    echo "   ✅ Template rendered successfully\n";
    echo "   HTML length: " . strlen($html) . " bytes\n";
    
    // Check for emojis in rendered HTML
    if (preg_match('/[\x{1F000}-\x{1F9FF}]/u', $html)) {
        echo "   ⚠️  Rendered HTML contains emoji!\n";
        
        // Try to find where
        preg_match_all('/[\x{1F000}-\x{1F9FF}]/u', $html, $matches);
        echo "   Found " . count($matches[0]) . " emoji character(s)\n";
    } else {
        echo "   ✅ No emojis in rendered HTML\n";
    }
    
    echo "\n";
    
} catch (\Exception $e) {
    echo "   ❌ Template rendering failed\n";
    echo "   Error: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}:{$e->getLine()}\n\n";
    exit(1);
}

// Test PDF generation
echo "Attempting PDF generation...\n";
try {
    $service = new InvoiceService();
    
    // Try to generate the PDF
    $response = $service->generateAdvanceInvoice($booking);
    
    echo "   ✅ SUCCESS! PDF generated without errors!\n\n";
    echo "   The PDF should download correctly from the admin panel.\n";
    echo "   Response type: " . get_class($response) . "\n\n";
    
} catch (\Dompdf\Exception $e) {
    echo "   ❌ DomPDF Error!\n";
    echo "   Message: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}\n";
    echo "   Line: {$e->getLine()}\n\n";
    
    echo "   This is a DomPDF-specific error.\n";
    echo "   Most likely cause: Special character that DomPDF can't handle.\n\n";
    
    // Try to extract the problematic content
    if (preg_match('/line (\d+)/', $e->getMessage(), $matches)) {
        echo "   Problem at line: {$matches[1]}\n";
    }
    
    exit(1);
    
} catch (\Exception $e) {
    echo "   ❌ FAILED!\n";
    echo "   Error Type: " . get_class($e) . "\n";
    echo "   Message: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}\n";
    echo "   Line: {$e->getLine()}\n\n";
    
    echo "   Stack trace:\n";
    $trace = $e->getTrace();
    foreach (array_slice($trace, 0, 5) as $i => $frame) {
        echo "   #{$i} ";
        if (isset($frame['file'])) {
            echo basename($frame['file']) . ":" . $frame['line'];
        }
        if (isset($frame['function'])) {
            echo " - " . $frame['function'] . "()";
        }
        echo "\n";
    }
    
    exit(1);
}

echo str_repeat("=", 80) . "\n";
echo "✅ All tests passed! The invoice system should work correctly.\n";
echo str_repeat("=", 80) . "\n\n";
