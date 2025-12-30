<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

/**
 * Invoice Service
 * 
 * Handles PDF invoice generation for bookings.
 * Supports both advance payment and balance due invoices.
 * Includes Arabic text support with proper RTL layout.
 * 
 * Features:
 * - Advance Payment Invoice (when booking is created)
 * - Balance Due Invoice (reminder for remaining payment)
 * - Bilingual support (Arabic/English)
 * - Platform branding
 * - Transaction details
 * 
 * @package App\Services
 */
class InvoiceService
{
    /**
     * Generate advance payment invoice PDF.
     * 
     * This invoice is issued when customer pays the advance amount
     * at booking time. It shows:
     * - Total booking amount
     * - Advance paid
     * - Balance remaining
     * - Payment details
     * 
     * @param Booking $booking The booking record
     * @return \Illuminate\Http\Response PDF download response
     */
    public function generateAdvanceInvoice(Booking $booking): \Illuminate\Http\Response
    {
        // Ensure booking has advance payment
        if (!$booking->isAdvancePayment()) {
            abort(400, 'This booking does not have advance payment.');
        }

        // Load relationships for invoice
        $booking->load(['hall.owner', 'hall.city.region', 'user', 'extraServices']);

        // Prepare invoice data
        $data = $this->prepareInvoiceData($booking, 'advance');

        // Generate PDF with A4 size and proper encoding
        $pdf = Pdf::loadView('invoices.advance-payment', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans'); // Supports Arabic

        // Generate filename
        $filename = "advance-invoice-{$booking->booking_number}.pdf";

        // Return PDF download
        return $pdf->download($filename);
    }

    /**
     * Generate balance due invoice PDF.
     * 
     * This invoice is issued as a reminder for the remaining balance
     * that must be paid before the event. It shows:
     * - Original booking details
     * - Advance already paid
     * - Outstanding balance
     * - Payment deadline
     * 
     * @param Booking $booking The booking record
     * @return \Illuminate\Http\Response PDF download response
     */
    public function generateBalanceInvoice(Booking $booking): \Illuminate\Http\Response
    {
        // Ensure booking has balance due
        if (!$booking->isAdvancePayment() || $booking->balance_due <= 0) {
            abort(400, 'This booking does not have a balance due.');
        }

        // Load relationships for invoice
        $booking->load(['hall.owner', 'hall.city.region', 'user', 'extraServices']);

        // Prepare invoice data
        $data = $this->prepareInvoiceData($booking, 'balance');

        // Generate PDF with A4 size and proper encoding
        $pdf = Pdf::loadView('invoices.balance-due', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans'); // Supports Arabic

        // Generate filename
        $filename = "balance-invoice-{$booking->booking_number}.pdf";

        // Return PDF download
        return $pdf->download($filename);
    }

    /**
     * Generate full payment receipt PDF.
     * 
     * This receipt is issued when booking is fully paid
     * (either as full payment or after balance is paid).
     * 
     * @param Booking $booking The booking record
     * @return \Illuminate\Http\Response PDF download response
     */
    public function generateFullReceipt(Booking $booking): \Illuminate\Http\Response
    {
        // Ensure booking is fully paid
        if (!$booking->isFullyPaid()) {
            abort(400, 'This booking is not fully paid yet.');
        }

        // Load relationships for receipt
        $booking->load(['hall.owner', 'hall.city.region', 'user', 'extraServices']);

        // Prepare receipt data
        $data = $this->prepareInvoiceData($booking, 'full');

        // Generate PDF
        $pdf = Pdf::loadView('invoices.full-receipt', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans');

        // Generate filename
        $filename = "receipt-{$booking->booking_number}.pdf";

        // Return PDF download
        return $pdf->download($filename);
    }

    /**
     * Prepare invoice data for PDF generation.
     * 
     * Centralizes data preparation to avoid duplication.
     * Formats numbers, translates fields, and structures data
     * for the invoice templates.
     * 
     * IMPORTANT - Type Casting for PHP 8.4 Strict Types:
     * Laravel's Eloquent returns DECIMAL database columns as strings.
     * With declare(strict_types=1), number_format() requires explicit float/int.
     * We MUST cast all numeric values to (float) before formatting.
     * 
     * Example error without casting:
     * number_format(): Argument #1 ($num) must be of type int|float, string given
     * 
     * @param Booking $booking The booking record
     * @param string $type Invoice type: 'advance', 'balance', or 'full'
     * @return array Structured invoice data
     */
    protected function prepareInvoiceData(Booking $booking, string $type): array
    {
        // Get hall name (handle translatable JSON)
        $hallName = is_array($booking->hall->name)
            ? ($booking->hall->name[app()->getLocale()] ?? $booking->hall->name['ar'] ?? 'N/A')
            : $booking->hall->name;

        // Get city and region names
        $cityName = is_array($booking->hall->city->name)
            ? ($booking->hall->city->name[app()->getLocale()] ?? $booking->hall->city->name['ar'] ?? 'N/A')
            : $booking->hall->city->name;

        $regionName = is_array($booking->hall->city->region->name)
            ? ($booking->hall->city->region->name[app()->getLocale()] ?? $booking->hall->city->region->name['ar'] ?? 'N/A')
            : $booking->hall->city->region->name;

        // Sanitize text fields to remove emojis and problematic characters
        $hallName = $this->sanitizeForPdf($hallName);
        $cityName = $this->sanitizeForPdf($cityName);
        $regionName = $this->sanitizeForPdf($regionName);

        // Format extra services
        $extraServices = $booking->extraServices->map(function ($service) {
            $serviceName = is_array($service->name)
                ? ($service->name[app()->getLocale()] ?? $service->name['ar'] ?? 'N/A')
                : $service->name;

            // Sanitize service name for PDF
            $serviceName = $this->sanitizeForPdf($serviceName);

            return [
                'name' => $serviceName,
                'quantity' => $service->pivot->quantity,
                'unit_price' => number_format((float) $service->pivot->unit_price, 3),
                'total_price' => number_format((float) $service->pivot->total_price, 3),
            ];
        });

        // Calculate payment deadline (e.g., 3 days before event)
        $paymentDeadline = \Carbon\Carbon::parse($booking->booking_date)->subDays(3);

        // Base data for all invoice types
        return [
            'booking' => $booking,
            'type' => $type,
            'hallName' => $hallName,
            'cityName' => $cityName,
            'regionName' => $regionName,
            'extraServices' => $extraServices,
            'ownerName' => $this->sanitizeForPdf($booking->hall->owner->name ?? 'N/A'),
            'ownerPhone' => $booking->hall->owner->phone ?? 'N/A',
            'paymentDeadline' => $paymentDeadline,
            'generatedDate' => now(),
            'platformName' => config('app.name', 'Majalis'),
            'platformPhone' => config('app.phone', '+968 9999 9999'),
            'platformEmail' => config('app.email', 'info@majalis.om'),
            'platformAddress' => config('app.address', 'Muscat, Oman'),
            
            // Sanitized booking data - CRITICAL for preventing UTF-8 errors
            'customerName' => $this->sanitizeForPdf($booking->customer_name ?? 'N/A'),
            'customerEmail' => $this->sanitizeForPdf($booking->customer_email ?? 'N/A'),
            'customerPhone' => $booking->customer_phone ?? 'N/A',
            'customerNotes' => $this->sanitizeForPdf($booking->customer_notes ?? ''),
            'eventType' => $this->sanitizeForPdf($booking->event_type ?? 'N/A'),
            'eventDetails' => $this->sanitizeForPdf(
                is_array($booking->event_details) 
                    ? json_encode($booking->event_details, JSON_UNESCAPED_UNICODE)
                    : ($booking->event_details ?? '')
            ),
            'userName' => $booking->user ? $this->sanitizeForPdf($booking->user->name) : null,
            
            // Formatted amounts - Explicitly cast to float for strict types compatibility
            'formattedHallPrice' => number_format((float) $booking->hall_price, 3),
            'formattedServicesPrice' => number_format((float) $booking->services_price, 3),
            'formattedSubtotal' => number_format((float) $booking->subtotal, 3),
            'formattedCommission' => number_format((float) $booking->commission_amount, 3),
            'formattedTotal' => number_format((float) $booking->total_amount, 3),
            'formattedAdvance' => number_format((float) ($booking->advance_amount ?? 0), 3),
            'formattedBalance' => number_format((float) ($booking->balance_due ?? 0), 3),
        ];
    }

    /**
     * Sanitize text for PDF generation.
     * 
     * Removes emoji and other characters that cause DomPDF encoding issues.
     * This prevents "Malformed UTF-8 characters" errors.
     * 
     * @param string|null $text Text to sanitize
     * @return string Sanitized text safe for PDF
     */
    protected function sanitizeForPdf(?string $text): string
    {
        // Handle null values
        if ($text === null || $text === '') {
            return '';
        }

        // Remove all emoji and problematic Unicode characters
        $text = preg_replace('/[\x{1F000}-\x{1F9FF}]/u', '', $text); // Emoji & Pictographs
        $text = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $text); // Miscellaneous Symbols
        $text = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $text); // Dingbats
        $text = preg_replace('/[\x{FE00}-\x{FE0F}]/u', '', $text); // Variation Selectors
        $text = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $text); // Misc Symbols and Pictographs
        $text = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $text); // Emoticons
        $text = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $text); // Transport and Map
        $text = preg_replace('/[\x{1F700}-\x{1F77F}]/u', '', $text); // Alchemical Symbols
        $text = preg_replace('/[\x{1F780}-\x{1F7FF}]/u', '', $text); // Geometric Shapes Extended
        $text = preg_replace('/[\x{1F800}-\x{1F8FF}]/u', '', $text); // Supplemental Arrows-C
        $text = preg_replace('/[\x{1F900}-\x{1F9FF}]/u', '', $text); // Supplemental Symbols and Pictographs
        $text = preg_replace('/[\x{2B50}-\x{2B55}]/u', '', $text); // Stars and other symbols
        
        // Remove zero-width characters and joiners
        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text);
        
        // Remove control characters except newlines and tabs
        $text = preg_replace('/[\x00-\x08\x0B-\x0C\x0E-\x1F\x7F]/u', '', $text);
        
        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }

    /**
     * Stream invoice PDF (for preview without download).
     * 
     * @param Booking $booking The booking record
     * @param string $type Invoice type: 'advance', 'balance', or 'full'
     * @return \Illuminate\Http\Response PDF stream response
     */
    public function streamInvoice(Booking $booking, string $type = 'advance'): \Illuminate\Http\Response
    {
        // Validate type
        if (!in_array($type, ['advance', 'balance', 'full'])) {
            abort(400, 'Invalid invoice type.');
        }

        // Load relationships
        $booking->load(['hall.owner', 'hall.city.region', 'user', 'extraServices']);

        // Prepare data
        $data = $this->prepareInvoiceData($booking, $type);

        // Select appropriate view
        $view = match ($type) {
            'advance' => 'invoices.advance-payment',
            'balance' => 'invoices.balance-due',
            'full' => 'invoices.full-receipt',
        };

        // Generate and stream PDF
        $pdf = Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->stream();
    }
}
