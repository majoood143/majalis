<?php

declare(strict_types=1);

namespace App\Services;

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
     * Download booking confirmation PDF as a browser response.
     *
     * Always regenerates with mPDF to guarantee Arabic renders correctly.
     *
     * @param Booking $booking
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Booking $booking): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $booking->load(['hall.city.region', 'extraServices', 'user']);

        $html = view('pdf.booking-confirmation', ['booking' => $booking])->render();

        $pdfService = new PdfExportService();
        $filename   = $booking->booking_number . '.pdf';

        return $pdfService->generateFromHtml($html)->download($filename);
    }
}
