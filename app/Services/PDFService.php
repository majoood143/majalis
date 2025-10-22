<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class PDFService
{
    /**
     * Generate booking invoice PDF
     */
    public function generateBookingInvoice(Booking $booking): string
    {
        try {
            $pdf = Pdf::loadView('pdf.invoice', [
                'booking' => $booking,
                'hall' => $booking->hall,
                'owner' => $booking->hall->owner,
                'extraServices' => $booking->extraServices,
            ]);

            $filename = 'invoices/' . $booking->booking_number . '.pdf';

            // Save to storage
            Storage::put($filename, $pdf->output());

            // Update booking with invoice path
            $booking->update(['invoice_path' => $filename]);

            Log::info('Invoice generated', ['booking_id' => $booking->id, 'filename' => $filename]);

            return $filename;
        } catch (Exception $e) {
            Log::error('Invoice generation failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate booking confirmation PDF
     */
    public function generateBookingConfirmation(Booking $booking): string
    {
        try {
            $pdf = Pdf::loadView('pdf.confirmation', [
                'booking' => $booking,
                'hall' => $booking->hall,
                'qrCode' => $this->generateQRCode($booking),
            ]);

            $filename = 'confirmations/' . $booking->booking_number . '.pdf';

            Storage::put($filename, $pdf->output());

            Log::info('Confirmation generated', ['booking_id' => $booking->id]);

            return $filename;
        } catch (Exception $e) {
            Log::error('Confirmation generation failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate owner payout report
     */
    public function generateOwnerPayoutReport(int $ownerId, string $startDate, string $endDate): string
    {
        try {
            $bookings = Booking::whereHas('hall', function ($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->with(['hall', 'extraServices'])
                ->get();

            $totalRevenue = $bookings->sum('total_amount');
            $totalCommission = $bookings->sum('commission_amount');
            $totalPayout = $bookings->sum('owner_payout');

            $pdf = Pdf::loadView('pdf.payout-report', [
                'bookings' => $bookings,
                'owner' => $bookings->first()?->hall->owner,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalRevenue' => $totalRevenue,
                'totalCommission' => $totalCommission,
                'totalPayout' => $totalPayout,
            ]);

            $filename = 'reports/payout-' . $ownerId . '-' . date('Y-m-d') . '.pdf';

            Storage::put($filename, $pdf->output());

            Log::info('Payout report generated', ['owner_id' => $ownerId]);

            return $filename;
        } catch (Exception $e) {
            Log::error('Payout report generation failed', [
                'owner_id' => $ownerId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate monthly revenue report
     */
    public function generateMonthlyRevenueReport(int $month, int $year): string
    {
        try {
            $startDate = "$year-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            $bookings = Booking::whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->with(['hall', 'user'])
                ->get();

            $stats = [
                'total_bookings' => $bookings->count(),
                'total_revenue' => $bookings->sum('total_amount'),
                'total_commission' => $bookings->sum('commission_amount'),
                'total_owner_payout' => $bookings->sum('owner_payout'),
                'by_hall' => $bookings->groupBy('hall_id')->map(function ($hallBookings) {
                    return [
                        'hall' => $hallBookings->first()->hall,
                        'count' => $hallBookings->count(),
                        'revenue' => $hallBookings->sum('total_amount'),
                    ];
                }),
            ];

            $pdf = Pdf::loadView('pdf.monthly-report', [
                'bookings' => $bookings,
                'stats' => $stats,
                'month' => $month,
                'year' => $year,
            ]);

            $filename = "reports/monthly-$year-$month.pdf";

            Storage::put($filename, $pdf->output());

            Log::info('Monthly report generated', ['month' => $month, 'year' => $year]);

            return $filename;
        } catch (Exception $e) {
            Log::error('Monthly report generation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Generate QR code for booking
     */
    protected function generateQRCode(Booking $booking): string
    {
        // You can use libraries like SimpleSoftwareIO/simple-qrcode
        // For now, return a placeholder
        return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==";
    }

    /**
     * Get PDF from storage
     */
    public function getPDF(string $path): ?string
    {
        if (Storage::exists($path)) {
            return Storage::path($path);
        }

        return null;
    }

    /**
     * Download PDF
     */
    public function downloadPDF(string $path, string $filename = 'document.pdf')
    {
        if (Storage::exists($path)) {
            return Storage::download($path, $filename);
        }

        abort(404, 'PDF not found');
    }

    /**
     * Delete PDF
     */
    public function deletePDF(string $path): bool
    {
        if (Storage::exists($path)) {
            return Storage::delete($path);
        }

        return false;
    }
}
