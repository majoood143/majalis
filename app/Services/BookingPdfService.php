<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * Service for generating booking confirmation PDFs
 *
 * @package App\Services
 */
class BookingPdfService
{
    /**
     * Generate booking confirmation PDF
     *
     * @param Booking $booking
     * @return string PDF file path
     * @throws Exception
     */
    public function generateConfirmation(Booking $booking): string
    {
        try {
            // Load booking with relationships
            $booking->load(['hall.city.region', 'extraServices', 'user']);

            // Generate PDF
            $pdf = Pdf::loadView('pdf.booking-confirmation', [
                'booking' => $booking,
            ]);

            // Set options for Arabic/RTL support
            $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
            $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->set_option('isFontSubsettingEnabled', true);
            $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

            // Set paper size and orientation
            $pdf->setPaper('a4', 'portrait');

            // Generate filename
            $filename = 'booking-confirmations/' . $booking->booking_number . '.pdf';

            // Save PDF to storage
            Storage::disk('public')->put($filename, $pdf->output());

            // Update booking with PDF path
            $booking->update([
                'invoice_path' => $filename
            ]);

            return $filename;
        } catch (Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage(), [
                'booking_id' => $booking->id
            ]);

            throw new Exception('Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download booking confirmation PDF
     *
     * @param Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function download(Booking $booking)
    {

        // Use the Arabic support method instead
        return $this->downloadWithArabicSupport($booking);
        // if ($booking->invoice_path && Storage::disk('public')->exists($booking->invoice_path)) {
        //     return Storage::disk('public')->download($booking->invoice_path, $booking->booking_number . '.pdf');
        // }

        // // Generate if doesn't exist
        // $path = $this->generateConfirmation($booking);
        // return Storage::disk('public')->download($path, $booking->booking_number . '.pdf');
    }

    /**
     * Download booking confirmation PDF with Arabic support
     *
     * @param Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function downloadWithArabicSupport(Booking $booking)
    {
        // Load booking with relationships
        $booking->load(['hall.city.region', 'extraServices', 'user']);

        // Generate HTML
        $html = view('pdf.booking-confirmation', ['booking' => $booking])->render();

        // Ensure UTF-8 encoding
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        // Generate PDF with proper encoding
        $pdf = Pdf::loadHTML($html);

        // Set options
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        $pdf->setPaper('a4', 'portrait');

        $filename = $booking->booking_number . '.pdf';

        return $pdf->download($filename);
    }
}
