<?php

declare(strict_types=1);

/**
 * Comprehensive UTF-8 Diagnostic
 * 
 * Checks EVERYTHING that could cause the UTF-8 error:
 * - Booking data ✅ (already checked)
 * - Translation files (__() calls)
 * - Config values (app.name, etc.)
 * - Blade templates (hidden characters)
 * - Date formatting
 * 
 * Usage:
 * php artisan tinker
 * include 'diagnose_comprehensive.php';
 * fullDiagnostic(1);
 */

use App\Models\Booking;

function hasEmoji(?string $text): bool
{
    if (!$text) return false;
    
    $patterns = [
        '/[\x{1F000}-\x{1F9FF}]/u',
        '/[\x{2600}-\x{26FF}]/u',
        '/[\x{2700}-\x{27BF}]/u',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text)) {
            return true;
        }
    }
    
    return false;
}

function fullDiagnostic(int $bookingId): void
{
    echo "\n🔍 COMPREHENSIVE UTF-8 DIAGNOSTIC\n";
    echo str_repeat("=", 80) . "\n\n";
    
    $issues = [];
    
    // ========================================
    // 1. Check Config Values
    // ========================================
    echo "1️⃣ Checking Config Values...\n";
    
    $configChecks = [
        'app.name' => config('app.name'),
        'app.phone' => config('app.phone'),
        'app.email' => config('app.email'),
        'app.address' => config('app.address'),
    ];
    
    foreach ($configChecks as $key => $value) {
        if ($value && hasEmoji($value)) {
            $issues["config::{$key}"] = $value;
            echo "   ❌ {$key}: \"{$value}\"\n";
        } else {
            echo "   ✅ {$key}: Clean\n";
        }
    }
    
    // ========================================
    // 2. Check Translation Files
    // ========================================
    echo "\n2️⃣ Checking Translation Files...\n";
    
    $translationKeys = [
        'Advance Paid',
        'Balance Due',
        'Important Notice',
        'Payment Status',
        'URGENT',
        'Bill To',
        'Hall Price',
        'Services Total',
        'Subtotal',
        'Platform Fee',
        'Total Amount',
    ];
    
    foreach ($translationKeys as $key) {
        $translated = __($key);
        if (hasEmoji($translated)) {
            $issues["translation::{$key}"] = $translated;
            echo "   ❌ '{$key}': \"{$translated}\"\n";
        }
    }
    
    if (empty($issues)) {
        echo "   ✅ All translations clean\n";
    }
    
    // ========================================
    // 3. Check Booking Data (Again for completeness)
    // ========================================
    echo "\n3️⃣ Checking Booking Data...\n";
    
    $booking = Booking::with(['hall', 'hall.city', 'hall.city.region', 'hall.owner', 'user', 'extraServices'])
        ->find($bookingId);
    
    if (!$booking) {
        echo "   ❌ Booking #{$bookingId} not found!\n";
        return;
    }
    
    $dataChecks = [
        'customer_name' => $booking->customer_name,
        'customer_email' => $booking->customer_email,
        'customer_phone' => $booking->customer_phone,
        'event_type' => $booking->event_type,
        'hall.name' => is_array($booking->hall->name) 
            ? json_encode($booking->hall->name) 
            : $booking->hall->name,
        'owner.name' => $booking->hall->owner->name ?? null,
        'user.name' => $booking->user->name ?? null,
    ];
    
    $dataClean = true;
    foreach ($dataChecks as $key => $value) {
        if ($value && hasEmoji($value)) {
            $issues["booking::{$key}"] = $value;
            echo "   ❌ {$key}: \"{$value}\"\n";
            $dataClean = false;
        }
    }
    
    if ($dataClean) {
        echo "   ✅ All booking data clean\n";
    }
    
    // ========================================
    // 4. Check Date Formatting
    // ========================================
    echo "\n4️⃣ Checking Date Formatting...\n";
    
    try {
        $dateStr = now()->format('d/m/Y H:i');
        if (hasEmoji($dateStr)) {
            $issues['date_format'] = $dateStr;
            echo "   ❌ Date format has emoji: {$dateStr}\n";
        } else {
            echo "   ✅ Date formatting clean\n";
        }
    } catch (\Exception $e) {
        echo "   ⚠️  Date check error: {$e->getMessage()}\n";
    }
    
    // ========================================
    // 5. Test PDF Generation Components
    // ========================================
    echo "\n5️⃣ Testing PDF Components...\n";
    
    // Test if DomPDF can handle basic content
    try {
        $testHtml = '<html><body><p>Test مرحبا</p></body></html>';
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($testHtml)
            ->setOption('defaultFont', 'DejaVu Sans');
        echo "   ✅ DomPDF basic test: PASSED\n";
    } catch (\Exception $e) {
        echo "   ❌ DomPDF basic test: FAILED - " . $e->getMessage() . "\n";
        $issues['dompdf_basic'] = $e->getMessage();
    }
    
    // Test with actual booking data
    try {
        $testData = [
            'customerName' => $booking->customer_name,
            'hallName' => is_array($booking->hall->name) 
                ? ($booking->hall->name['en'] ?? 'Test Hall')
                : $booking->hall->name,
        ];
        
        $testHtml2 = '<html><body>' . 
                     '<p>' . $testData['customerName'] . '</p>' .
                     '<p>' . $testData['hallName'] . '</p>' .
                     '</body></html>';
        
        $pdf2 = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($testHtml2)
            ->setOption('defaultFont', 'DejaVu Sans');
        echo "   ✅ DomPDF with booking data: PASSED\n";
    } catch (\Exception $e) {
        echo "   ❌ DomPDF with booking data: FAILED\n";
        echo "      Error: " . $e->getMessage() . "\n";
        $issues['dompdf_booking'] = $e->getMessage();
    }
    
    // ========================================
    // 6. Check Blade Template Files
    // ========================================
    echo "\n6️⃣ Checking Blade Template Files...\n";
    
    $templates = [
        'advance-payment' => resource_path('views/invoices/advance-payment.blade.php'),
        'balance-due' => resource_path('views/invoices/balance-due.blade.php'),
        'full-receipt' => resource_path('views/invoices/full-receipt.blade.php'),
    ];
    
    foreach ($templates as $name => $path) {
        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            // Check file encoding
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ASCII', 'ISO-8859-1'], true);
            
            if ($encoding !== 'UTF-8') {
                echo "   ⚠️  {$name}: Encoding is {$encoding} (should be UTF-8)\n";
                $issues["template::{$name}::encoding"] = $encoding;
            }
            
            // Check for emoji
            if (hasEmoji($content)) {
                echo "   ❌ {$name}: Contains emoji characters!\n";
                $issues["template::{$name}::emoji"] = true;
                
                // Try to find the emoji
                preg_match('/[\x{1F000}-\x{1F9FF}]/u', $content, $matches);
                if (!empty($matches)) {
                    echo "      Found emoji: " . $matches[0] . "\n";
                }
            } else {
                echo "   ✅ {$name}: No emoji found\n";
            }
        } else {
            echo "   ⚠️  {$name}: File not found at {$path}\n";
        }
    }
    
    // ========================================
    // SUMMARY
    // ========================================
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "📊 DIAGNOSTIC SUMMARY\n";
    echo str_repeat("=", 80) . "\n";
    
    if (empty($issues)) {
        echo "✅ NO ISSUES FOUND!\n\n";
        echo "Everything looks clean. The UTF-8 error might be caused by:\n";
        echo "1. Cached views - Run: php artisan view:clear\n";
        echo "2. PHP OPcache - Restart PHP-FPM\n";
        echo "3. Old InvoiceService.php - Make sure you're using the latest version\n\n";
    } else {
        echo "❌ FOUND " . count($issues) . " ISSUE(S):\n\n";
        
        foreach ($issues as $key => $value) {
            echo "  • {$key}\n";
            if (is_string($value)) {
                echo "    Value: " . substr($value, 0, 100) . "\n";
            }
        }
        
        echo "\n💡 RECOMMENDED ACTIONS:\n";
        
        if (isset($issues['config::app.name'])) {
            echo "  1. Update config/app.php - remove emojis from app name\n";
        }
        
        if (array_key_exists('translation::Advance Paid', $issues)) {
            echo "  2. Check translation files - resources/lang/*/advance_payment.php\n";
        }
        
        if (str_contains(implode('', array_keys($issues)), 'template')) {
            echo "  3. Replace blade templates with emoji-free versions\n";
        }
    }
    
    echo "\n";
}

// Quick check function
function quickCheck(int $bookingId): void
{
    echo "🚀 Quick UTF-8 Check for Booking #{$bookingId}\n\n";
    
    $booking = Booking::with(['hall'])->find($bookingId);
    
    if (!$booking) {
        echo "❌ Booking not found\n";
        return;
    }
    
    // Try to generate the actual PDF
    try {
        $service = new \App\Services\InvoiceService();
        
        echo "Attempting PDF generation...\n";
        $pdf = $service->generateAdvanceInvoice($booking);
        
        echo "✅ SUCCESS! PDF generated without errors.\n";
        echo "The invoice should download correctly.\n";
    } catch (\Exception $e) {
        echo "❌ FAILED! Error occurred:\n";
        echo "   " . $e->getMessage() . "\n\n";
        
        // Extract line number if available
        if (preg_match('/line (\d+)/', $e->getMessage(), $matches)) {
            echo "   Problem at line: " . $matches[1] . "\n";
        }
        
        echo "\nStack trace:\n";
        echo $e->getTraceAsString() . "\n";
    }
}
