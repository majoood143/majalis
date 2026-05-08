<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Payment Receipt Email
 *
 * Sends payment receipt PDF to customer via email.
 *
 * @package App\Mail
 */
class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The payment instance
     *
     * @var Payment
     */
    public Payment $payment;

    /**
     * Create a new message instance.
     *
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        $ref = $this->payment->payment_reference;
        if (is_array($ref)) {
            $ref = $ref[app()->getLocale()] ?? $ref['ar'] ?? $ref['en'] ?? 'N/A';
        }

        return new Envelope(
            subject: 'Payment Receipt - ' . ($ref ?? 'N/A'),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        // Load relationships
        $this->payment->load('booking.hall');

        return new Content(
            view: 'emails.payment-receipt',
            with: [
                'payment' => $this->payment,
                'booking' => $this->payment->booking,
                'hall' => $this->payment->booking?->hall,
                'customerName' => (function ($name) {
                    if (is_array($name)) {
                        return $name[app()->getLocale()] ?? $name['ar'] ?? $name['en'] ?? 'Valued Customer';
                    }
                    return $name ?? 'Valued Customer';
                })($this->payment->booking?->customer_name),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        // Load relationships if not loaded
        $this->payment->load('booking.hall');

        // Setting::get() returns JSON-decoded values which may be arrays.
        // Resolve them to plain strings here so the PDF view never receives an array.
        $locale = app()->getLocale();
        $str = function (mixed $v, string $fallback) use ($locale): string {
            if (is_array($v)) {
                $v = $v[$locale] ?? $v['ar'] ?? $v['en'] ?? $fallback;
            }
            if (is_string($v) && $v !== '') return $v;
            if (is_numeric($v)) return (string) $v;
            return $fallback;
        };

        // Generate PDF content at send time (not in constructor)
        $pdfContent = Pdf::loadView('pdf.payment-receipt', [
            'payment'         => $this->payment,
            'booking'         => $this->payment->booking,
            'hall'            => $this->payment->booking?->hall,
            'generatedDate'   => now(),
            'platformName'    => $str(Setting::get('general', 'site_name'), 'Majalis'),
            'platformPhone'   => $str(Setting::get('contact', 'phone'), '+968 9999 9999'),
            'platformEmail'   => $str(Setting::get('contact', 'email'), 'info@majalis.om'),
            'platformAddress' => $str(Setting::get('contact', 'address'), 'Muscat, Oman'),
        ])->output();

        $filename = 'receipt_' . $this->payment->payment_reference . '.pdf';

        return [
            Attachment::fromData(fn() => $pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
