<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * Service for generating booking confirmation PDFs.
 *
 * Uses PdfExportService (mPDF + Tajawal) for proper Arabic/RTL rendering.
 * DomPDF was replaced because DejaVu Sans has no Arabic glyph support.
 *
 * @package App\Services
 */
class BookingPdfService
{
    /**
     * Generate booking confirmation PDF and save to storage.
     *
     * @param Booking $booking
     * @return string PDF file path relative to the public disk
     * @throws Exception
     */
    public function generateConfirmation(Booking $booking): string
    {
        try {
            // Load booking with relationships
            $booking->load(['hall.city.region', 'extraServices', 'user']);

            // Ensure every booking has a guest_token so the QR code URL works
            // for both guest and authenticated-user bookings.
            $this->ensureGuestToken($booking);

            // Render blade view to HTML
            $html = view('pdf.booking-confirmation', ['booking' => $booking])->render();

            // Generate PDF via mPDF (Tajawal font, RTL, Arabic OTL)
            $pdfService = new PdfExportService();
            $pdfBinary  = $pdfService->generateFromHtml($html)->output();

            // Save to storage/app/public/booking-confirmations/
            $filename = 'booking-confirmations/' . $booking->booking_number . '.pdf';
            Storage::disk('public')->put($filename, $pdfBinary);

            // Persist path on the booking record
            $booking->update(['invoice_path' => $filename]);

            return $filename;
        } catch (Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
            ]);

            throw new Exception('Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Ensure the booking has a guest_token so the PDF QR code can link to
     * the public /guest/booking/{token} route for both guest and user bookings.
     */
    private function ensureGuestToken(Booking $booking): void
    {
        if (!empty($booking->guest_token)) {
            return;
        }

        $token = Booking::generateGuestToken();

        $booking->update(['guest_token' => $token]);
        $booking->guest_token = $token;
    }

    /**
     * Download booking confirmation PDF as a browser response.
     *
     * Always regenerates with mPDF to guarantee Arabic renders correctly.
     *
     * @param Booking $booking
     * @return StreamedResponse
     */
    public function download(Booking $booking): StreamedResponse
    {
        $booking->load(['hall.city.region', 'extraServices', 'user']);

        $this->ensureGuestToken($booking);

        $html = view('pdf.booking-confirmation', ['booking' => $booking])->render();

        $pdfService = new PdfExportService();
        $filename   = $booking->booking_number . '.pdf';

        return $pdfService->generateFromHtml($html)->download($filename);
    }
}
