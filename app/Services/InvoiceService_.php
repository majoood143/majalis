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
class InvoiceService1
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

        // Format extra services
        $extraServices = $booking->extraServices->map(function ($service) {
            $serviceName = is_array($service->name)
                ? ($service->name[app()->getLocale()] ?? $service->name['ar'] ?? 'N/A')
                : $service->name;

            return [
                'name' => $serviceName,
                'quantity' => $service->pivot->quantity,
                'unit_price' => number_format($service->pivot->unit_price, 3),
                'total_price' => number_format($service->pivot->total_price, 3),
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
            'ownerName' => $booking->hall->owner->name ?? 'N/A',
            'ownerPhone' => $booking->hall->owner->phone ?? 'N/A',
            'paymentDeadline' => $paymentDeadline,
            'generatedDate' => now(),
            'platformName' => config('app.name', 'Majalis'),
            'platformPhone' => config('app.phone', '+968 9999 9999'),
            'platformEmail' => config('app.email', 'info@majalis.om'),
            'platformAddress' => config('app.address', 'Muscat, Oman'),

            // Formatted amounts
            'formattedHallPrice' => (float) number_format($booking->hall_price, 3),
            'formattedServicesPrice' => number_format($booking->services_price, 3),
            'formattedSubtotal' => number_format($booking->subtotal, 3),
            'formattedCommission' => number_format($booking->commission_amount, 3),
            'formattedTotal' => number_format($booking->total_amount, 3),
            'formattedAdvance' => number_format($booking->advance_amount, 3),
            'formattedBalance' => number_format($booking->balance_due, 3),
        ];
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
