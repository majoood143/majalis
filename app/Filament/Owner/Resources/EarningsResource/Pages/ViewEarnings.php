<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\EarningsResource\Pages;

use App\Filament\Owner\Resources\EarningsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

/**
 * ViewEarnings Page for Owner Panel
 *
 * Displays detailed view of a single earning/booking.
 *
 * @package App\Filament\Owner\Resources\EarningsResource\Pages
 */
class ViewEarnings extends ViewRecord
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = EarningsResource::class;

    /**
     * Get the page title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return __('owner.earnings.view_title', [
            'number' => $this->record->booking_number,
        ]);
    }

    /**
     * Get the page heading.
     *
     * @return string
     */
    public function getHeading(): string
    {
        return $this->record->booking_number;
    }

    /**
     * Get the page subheading.
     *
     * @return string|null
     */
    public function getSubheading(): ?string
    {
        $hallName = is_array($this->record->hall->name)
            ? ($this->record->hall->name[app()->getLocale()] ?? $this->record->hall->name['en'])
            : $this->record->hall->name;

        return __('owner.earnings.view_subheading', [
            'hall' => $hallName,
            'date' => $this->record->booking_date->format('F j, Y'),
            'amount' => number_format((float) $this->record->owner_payout, 3),
        ]);
    }

    /**
     * Get header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Download Invoice
            Actions\Action::make('downloadInvoice')
                ->label(__('owner.earnings.download_invoice'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->visible(fn (): bool => !empty($this->record->invoice_path))
                ->action(function (): void {
                    // Return file download
                    if (Storage::exists($this->record->invoice_path)) {
                        redirect(Storage::url($this->record->invoice_path));
                    } else {
                        Notification::make()
                            ->warning()
                            ->title(__('owner.earnings.invoice_not_found'))
                            ->send();
                    }
                }),

            // Generate Statement
            Actions\Action::make('generateStatement')
                ->label(__('owner.earnings.generate_statement'))
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->action(function (): void {
                    $this->generateBookingStatement();
                }),

            // Back to list
            Actions\Action::make('backToList')
                ->label(__('owner.actions.back_to_list'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(EarningsResource::getUrl('index')),
        ];
    }

    /**
     * Get the breadcrumb label.
     *
     * @return string
     */
    public function getBreadcrumb(): string
    {
        return $this->record->booking_number;
    }

    /**
     * Generate a statement for this booking.
     *
     * @return void
     */
    protected function generateBookingStatement(): void
    {
        try {
            $booking = $this->record->load(['hall', 'extraServices', 'user']);

            $hallName = is_array($booking->hall->name)
                ? ($booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'])
                : $booking->hall->name;

            // Generate PDF
            $pdf = Pdf::loadView('pdf.booking-statement', [
                'booking' => $booking,
                'hallName' => $hallName,
                'owner' => auth()->user(),
                'hallOwner' => auth()->user()->hallOwner,
                'generatedAt' => now(),
            ])->setPaper('a4');

            // Ensure directory exists
            if (!Storage::disk('public')->exists('statements')) {
                Storage::disk('public')->makeDirectory('statements');
            }

            // Save file
            $filename = 'statement-' . $booking->booking_number . '-' . now()->format('Ymd') . '.pdf';
            $filepath = 'statements/' . $filename;

            Storage::disk('public')->put($filepath, $pdf->output());

            // Success notification with download link
            Notification::make()
                ->success()
                ->title(__('owner.earnings.statement_generated'))
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label(__('owner.actions.download'))
                        ->url(Storage::disk('public')->url($filepath))
                        ->openUrlInNewTab(),
                ])
                ->persistent()
                ->send();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Statement generation failed', [
                'booking_id' => $this->record->id,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('owner.earnings.statement_failed'))
                ->body($e->getMessage())
                ->send();
        }
    }
}
