<?php

declare(strict_types=1);

/**
 * UTF-8 Diagnostic Script
 * 
 * Run this script to identify which fields in your bookings contain
 * problematic characters (emojis, special Unicode) that cause PDF errors.
 * 
 * Usage:
 * php artisan tinker
 * include 'path/to/diagnose_utf8.php';
 * diagnoseBooking(1); // Replace 1 with your booking ID
 */

use App\Models\Booking;

/**
 * Check if a string contains emojis or problematic Unicode
 */
function hasProblematicCharacters(string $text): bool
{
    // Check for emoji and problematic Unicode ranges
    $patterns = [
        '/[\x{1F000}-\x{1F9FF}]/u',  // Emoji & Pictographs
        '/[\x{2600}-\x{26FF}]/u',    // Miscellaneous Symbols
        '/[\x{2700}-\x{27BF}]/u',    // Dingbats
        '/[\x{FE00}-\x{FE0F}]/u',    // Variation Selectors
        '/[\x{200B}-\x{200D}]/u',    // Zero-width characters
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Extract problematic characters from a string
 */
function extractProblematicCharacters(string $text): array
{
    $problematic = [];
    
    // Split string into individual characters
    $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
    
    foreach ($chars as $char) {
        // Check if character is outside basic Latin/Arabic range
        $code = mb_ord($char, 'UTF-8');
        
        // Skip basic Latin (0-127), Arabic (1536-1791), common punctuation
        if ($code > 127 && ($code < 1536 || $code > 1791)) {
            if ($code < 128 || $code > 255) { // Extended Latin is usually OK
                $problematic[] = [
                    'char' => $char,
                    'unicode' => sprintf('U+%04X', $code),
                    'decimal' => $code,
                ];
            }
        }
    }
    
    return $problematic;
}

/**
 * Diagnose a specific booking for UTF-8 issues
 */
function diagnoseBooking(int $bookingId): void
{
    echo "🔍 UTF-8 Diagnostic for Booking #{$bookingId}\n";
    echo str_repeat("=", 80) . "\n\n";
    
    $booking = Booking::with(['hall', 'hall.city', 'hall.city.region', 'hall.owner', 'user', 'extraServices'])
        ->find($bookingId);
    
    if (!$booking) {
        echo "❌ Booking #{$bookingId} not found.\n";
        return;
    }
    
    $issues = [];
    
    // Check customer fields
    $customerFields = [
        'customer_name' => $booking->customer_name,
        'customer_email' => $booking->customer_email,
        'customer_phone' => $booking->customer_phone,
        'customer_notes' => $booking->customer_notes,
    ];
    
    foreach ($customerFields as $field => $value) {
        if ($value && hasProblematicCharacters($value)) {
            $issues[$field] = [
                'value' => $value,
                'problematic_chars' => extractProblematicCharacters($value),
            ];
        }
    }
    
    // Check event fields
    if ($booking->event_type && hasProblematicCharacters($booking->event_type)) {
        $issues['event_type'] = [
            'value' => $booking->event_type,
            'problematic_chars' => extractProblematicCharacters($booking->event_type),
        ];
    }
    
    // Check hall name
    $hallName = is_array($booking->hall->name) 
        ? ($booking->hall->name['en'] ?? $booking->hall->name['ar'] ?? '')
        : $booking->hall->name;
    
    if ($hallName && hasProblematicCharacters($hallName)) {
        $issues['hall.name'] = [
            'value' => $hallName,
            'problematic_chars' => extractProblematicCharacters($hallName),
        ];
    }
    
    // Check city name
    $cityName = is_array($booking->hall->city->name)
        ? ($booking->hall->city->name['en'] ?? $booking->hall->city->name['ar'] ?? '')
        : $booking->hall->city->name;
    
    if ($cityName && hasProblematicCharacters($cityName)) {
        $issues['hall.city.name'] = [
            'value' => $cityName,
            'problematic_chars' => extractProblematicCharacters($cityName),
        ];
    }
    
    // Check region name
    $regionName = is_array($booking->hall->city->region->name)
        ? ($booking->hall->city->region->name['en'] ?? $booking->hall->city->region->name['ar'] ?? '')
        : $booking->hall->city->region->name;
    
    if ($regionName && hasProblematicCharacters($regionName)) {
        $issues['hall.city.region.name'] = [
            'value' => $regionName,
            'problematic_chars' => extractProblematicCharacters($regionName),
        ];
    }
    
    // Check owner name
    if ($booking->hall->owner && $booking->hall->owner->name && hasProblematicCharacters($booking->hall->owner->name)) {
        $issues['hall.owner.name'] = [
            'value' => $booking->hall->owner->name,
            'problematic_chars' => extractProblematicCharacters($booking->hall->owner->name),
        ];
    }
    
    // Check user name
    if ($booking->user && $booking->user->name && hasProblematicCharacters($booking->user->name)) {
        $issues['user.name'] = [
            'value' => $booking->user->name,
            'problematic_chars' => extractProblematicCharacters($booking->user->name),
        ];
    }
    
    // Check extra services
    foreach ($booking->extraServices as $index => $service) {
        $serviceName = is_array($service->name)
            ? ($service->name['en'] ?? $service->name['ar'] ?? '')
            : $service->name;
        
        if ($serviceName && hasProblematicCharacters($serviceName)) {
            $issues["extraServices[{$index}].name"] = [
                'value' => $serviceName,
                'problematic_chars' => extractProblematicCharacters($serviceName),
            ];
        }
    }
    
    // Display results
    if (empty($issues)) {
        echo "✅ No problematic characters found in this booking!\n";
        echo "   All text fields are safe for PDF generation.\n\n";
        return;
    }
    
    echo "❌ Found " . count($issues) . " field(s) with problematic characters:\n\n";
    
    foreach ($issues as $field => $data) {
        echo "Field: {$field}\n";
        echo "Value: \"{$data['value']}\"\n";
        echo "Problematic characters:\n";
        
        foreach ($data['problematic_chars'] as $char) {
            echo "  - '{$char['char']}' ({$char['unicode']}, decimal {$char['decimal']})\n";
        }
        
        echo "\n";
    }
    
    echo str_repeat("=", 80) . "\n";
    echo "💡 SOLUTION: The InvoiceService.php sanitizeForPdf() method should\n";
    echo "   automatically remove these characters. If you're still getting errors,\n";
    echo "   make sure you're using the latest version of InvoiceService.php\n";
    echo "   and that you've cleared all caches.\n";
}

/**
 * Scan all recent bookings for UTF-8 issues
 */
function scanAllBookings(int $limit = 20): void
{
    echo "🔍 Scanning last {$limit} bookings for UTF-8 issues\n";
    echo str_repeat("=", 80) . "\n\n";
    
    $bookings = Booking::with(['hall', 'hall.city', 'hall.city.region', 'hall.owner', 'user', 'extraServices'])
        ->latest()
        ->limit($limit)
        ->get();
    
    $totalIssues = 0;
    $bookingsWithIssues = [];
    
    foreach ($bookings as $booking) {
        $hasIssue = false;
        
        // Quick check of main fields
        $fields = [
            $booking->customer_name,
            $booking->customer_email,
            $booking->event_type,
            is_array($booking->hall->name) ? json_encode($booking->hall->name) : $booking->hall->name,
        ];
        
        foreach ($fields as $value) {
            if ($value && hasProblematicCharacters($value)) {
                $hasIssue = true;
                $totalIssues++;
                break;
            }
        }
        
        if ($hasIssue) {
            $bookingsWithIssues[] = $booking->id;
        }
    }
    
    if (empty($bookingsWithIssues)) {
        echo "✅ All {$limit} recent bookings are clean!\n";
        echo "   No problematic characters found.\n\n";
        return;
    }
    
    echo "❌ Found issues in " . count($bookingsWithIssues) . " booking(s):\n";
    echo "   Booking IDs: " . implode(', ', $bookingsWithIssues) . "\n\n";
    echo "💡 Run diagnoseBooking(ID) for detailed analysis of each booking.\n";
}

// If running directly from command line
if (php_sapi_name() === 'cli' && isset($argv[1])) {
    require __DIR__ . '/vendor/autoload.php';
    
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    if ($argv[1] === 'scan') {
        scanAllBookings(isset($argv[2]) ? (int)$argv[2] : 20);
    } elseif (is_numeric($argv[1])) {
        diagnoseBooking((int)$argv[1]);
    } else {
        echo "Usage:\n";
        echo "  php diagnose_utf8.php <booking_id>  - Diagnose specific booking\n";
        echo "  php diagnose_utf8.php scan [limit]  - Scan recent bookings\n";
    }
}
