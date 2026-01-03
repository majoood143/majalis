<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PayoutResource\Pages;

use App\Enums\PayoutStatus;
use App\Filament\Owner\Resources\PayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

/**
 * ViewPayout Page for Owner Panel
 *
 * Displays detailed view of a single payout.
 *
 * @package App\Filament\Owner\Resources\PayoutResource\Pages
 */
class ViewPayout extends ViewRecord
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = PayoutResource::class;

    /**
     * Get the page title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return __('owner.payouts.view_title', [
            'number' => $this->record->payout_number,
        ]);
    }

    /**
     * Get the page heading.
     *
     * @return string
     */
    public function getHeading(): string
    {
        return $this->record->payout_number;
    }

    /**
     * Get the page subheading.
     *
     * @return string|null
     */
    public function getSubheading(): ?string
    {
        return __('owner.payouts.view_subheading', [
            'period' => $this->record->period_start->format('M d') . ' - ' .
                $this->record->period_end->format('M d, Y'),
            'amount' => number_format((float) $this->record->net_payout, 3),
            'status' => $this->record->status->getLabel(),
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
            // Download receipt (if completed)
            Actions\Action::make('downloadReceipt')
                ->label(__('owner.payouts.download_receipt'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->visible(fn (): bool => $this->record->status === PayoutStatus::COMPLETED
                    && !empty($this->record->receipt_path))
                ->action(function (): void {
                    if (Storage::disk('public')->exists($this->record->receipt_path)) {
                        redirect(Storage::disk('public')->url($this->record->receipt_path));
                    } else {
                        Notification::make()
                            ->warning()
                            ->title(__('owner.payouts.receipt_not_found'))
                            ->send();
                    }
                }),

            // Report issue
            Actions\Action::make('reportIssue')
                ->label(__('owner.payouts.report_issue'))
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->visible(fn (): bool => $this->record->status === PayoutStatus::COMPLETED)
                ->url(fn (): string => route('filament.owner.resources.tickets.create', [
                    'subject' => 'Payout Issue: ' . $this->record->payout_number,
                    'related_booking' => null,
                ]))
                ->openUrlInNewTab(),

            // Back to list
            Actions\Action::make('backToList')
                ->label(__('owner.actions.back_to_list'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(PayoutResource::getUrl('index')),
        ];
    }

    /**
     * Get the breadcrumb label.
     *
     * @return string
     */
    public function getBreadcrumb(): string
    {
        return $this->record->payout_number;
    }
}
