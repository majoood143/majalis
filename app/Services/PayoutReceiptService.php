<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OwnerPayout;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * PayoutReceiptService
 *
 * Handles generation of payout receipt PDFs for hall owners.
 * Generates professional receipts with bilingual support (English/Arabic).
 *
 * Features:
 * - Generate receipt PDF on payout completion
 * - Store receipt in public storage
 * - Update payout record with receipt path
 * - Support for Arabic RTL content
 *
 * @package App\Services
 */
class PayoutReceiptService
{
    /**
     * Storage disk to use for receipts.
     *
     * @var string
     */
    protected string $disk = 'public';

    /**
     * Directory to store receipts.
     *
     * @var string
     */
    protected string $directory = 'receipts/payouts';

    /**
     * Generate payout receipt PDF and save to storage.
     *
     * Creates a professional receipt PDF with all payout details,
     * saves it to storage, and updates the payout record with the path.
     *
     * @param OwnerPayout $payout The completed payout record
     * @return string The storage path to the generated receipt
     * @throws Exception If PDF generation fails
     */
    public function generateReceipt(OwnerPayout $payout): string
    {
        try {
            // Load relationships for the receipt
            $payout->load([
                'owner',
                'hallOwner',
                'processor',
            ]);

            // Prepare receipt data
            $data = $this->prepareReceiptData($payout);

            // Generate PDF with proper settings for Arabic support
            $pdf = Pdf::loadView('pdf.payout-receipt', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('defaultFont', 'DejaVu Sans');

            // Ensure directory exists
            if (!Storage::disk($this->disk)->exists($this->directory)) {
                Storage::disk($this->disk)->makeDirectory($this->directory);
            }

            // Generate unique filename
            $filename = $this->generateFilename($payout);
            $path = $this->directory . '/' . $filename;

            // Save PDF to storage
            Storage::disk($this->disk)->put($path, $pdf->output());

            // Update payout record with receipt path
            $payout->update(['receipt_path' => $path]);

            // Log successful generation
            Log::info('Payout receipt generated successfully', [
                'payout_id' => $payout->id,
                'payout_number' => $payout->payout_number,
                'path' => $path,
            ]);

            return $path;
        } catch (Exception $e) {
            Log::error('Failed to generate payout receipt', [
                'payout_id' => $payout->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new Exception('Failed to generate payout receipt: ' . $e->getMessage());
        }
    }

    /**
     * Prepare data for the receipt template.
     *
     * Formats all financial values and prepares display data
     * for the PDF template.
     *
     * @param OwnerPayout $payout The payout record
     * @return array<string, mixed> Prepared data for the template
     */
    protected function prepareReceiptData(OwnerPayout $payout): array
    {
        return [
            // Payout Information
            'payout' => $payout,
            'payout_number' => $payout->payout_number,
            'status' => $payout->status->getLabel(),
            'status_color' => $payout->status->getColor(),

            // Period Information
            'period_start' => $payout->period_start->format('d M Y'),
            'period_end' => $payout->period_end->format('d M Y'),
            'period_string' => $payout->period_start->format('d M Y') . ' - ' . $payout->period_end->format('d M Y'),

            // Financial Details (with proper type casting for PHP 8.4)
            'gross_revenue' => number_format((float) $payout->gross_revenue, 3),
            'commission_amount' => number_format((float) $payout->commission_amount, 3),
            'commission_rate' => number_format((float) $payout->commission_rate, 2),
            'adjustments' => number_format((float) $payout->adjustments, 3),
            'net_payout' => number_format((float) $payout->net_payout, 3),
            'bookings_count' => $payout->bookings_count,

            // Payment Details
            'payment_method' => $payout->payment_method 
                ? __('admin.payout.methods.' . $payout->payment_method) 
                : '-',
            'transaction_reference' => $payout->transaction_reference ?? '-',
            'completed_at' => $payout->completed_at?->format('d M Y H:i') ?? '-',

            // Owner Information
            'owner_name' => $payout->owner?->name ?? 'Unknown',
            'owner_email' => $payout->owner?->email ?? '-',
            'business_name' => $payout->hallOwner?->business_name ?? '-',
            'bank_name' => $payout->hallOwner?->bank_name ?? '-',
            'bank_account' => $this->maskBankAccount($payout->hallOwner?->bank_account),

            // Processor Information
            'processed_by' => $payout->processor?->name ?? 'System',
            'processed_at' => $payout->processed_at?->format('d M Y H:i') ?? '-',

            // Document Meta
            'generated_at' => now()->format('d M Y H:i'),
            'currency' => 'OMR',

            // Company Information (from config or hardcoded)
            'company_name' => config('app.name', 'Majalis'),
            'company_address' => config('majalis.company_address', 'Muscat, Oman'),
            'company_phone' => config('majalis.company_phone', '+968 XXXX XXXX'),
            'company_email' => config('majalis.company_email', 'support@majalis.om'),
        ];
    }

    /**
     * Generate unique filename for the receipt.
     *
     * @param OwnerPayout $payout The payout record
     * @return string Generated filename
     */
    protected function generateFilename(OwnerPayout $payout): string
    {
        return sprintf(
            'payout-receipt-%s-%s.pdf',
            $payout->payout_number,
            now()->format('YmdHis')
        );
    }

    /**
     * Mask bank account number for security.
     *
     * Shows only last 4 digits of the account number.
     *
     * @param string|null $accountNumber The bank account number
     * @return string Masked account number
     */
    protected function maskBankAccount(?string $accountNumber): string
    {
        if (empty($accountNumber)) {
            return '-';
        }

        $length = strlen($accountNumber);
        if ($length <= 4) {
            return $accountNumber;
        }

        return str_repeat('*', $length - 4) . substr($accountNumber, -4);
    }

    /**
     * Delete receipt file from storage.
     *
     * @param OwnerPayout $payout The payout record
     * @return bool True if deleted successfully
     */
    public function deleteReceipt(OwnerPayout $payout): bool
    {
        if (empty($payout->receipt_path)) {
            return false;
        }

        if (Storage::disk($this->disk)->exists($payout->receipt_path)) {
            $deleted = Storage::disk($this->disk)->delete($payout->receipt_path);

            if ($deleted) {
                $payout->update(['receipt_path' => null]);
                Log::info('Payout receipt deleted', [
                    'payout_id' => $payout->id,
                    'path' => $payout->receipt_path,
                ]);
            }

            return $deleted;
        }

        return false;
    }

    /**
     * Regenerate receipt for a payout.
     *
     * Deletes existing receipt and generates a new one.
     *
     * @param OwnerPayout $payout The payout record
     * @return string Path to the new receipt
     * @throws Exception If regeneration fails
     */
    public function regenerateReceipt(OwnerPayout $payout): string
    {
        // Delete existing receipt if present
        $this->deleteReceipt($payout);

        // Generate new receipt
        return $this->generateReceipt($payout);
    }

    /**
     * Check if receipt exists for a payout.
     *
     * @param OwnerPayout $payout The payout record
     * @return bool True if receipt exists
     */
    public function receiptExists(OwnerPayout $payout): bool
    {
        if (empty($payout->receipt_path)) {
            return false;
        }

        return Storage::disk($this->disk)->exists($payout->receipt_path);
    }

    /**
     * Get the full URL to the receipt.
     *
     * @param OwnerPayout $payout The payout record
     * @return string|null URL to the receipt or null if not exists
     */
    public function getReceiptUrl(OwnerPayout $payout): ?string
    {
        if (!$this->receiptExists($payout)) {
            return null;
        }

        return Storage::disk($this->disk)->url($payout->receipt_path);
    }
}
