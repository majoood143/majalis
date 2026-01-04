<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PayoutResource\Pages;

use App\Enums\PayoutStatus;
use App\Filament\Admin\Resources\PayoutResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

/**
 * ViewPayout Page
 *
 * Displays detailed payout information with workflow actions.
 *
 * @package App\Filament\Admin\Resources\PayoutResource\Pages
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
     * Get the header actions for this page.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            // Edit Action
            Actions\EditAction::make()
                ->visible(fn () => !$this->record->status->isTerminal()),

            // Process Action
            Actions\Action::make('process')
                ->label(__('admin.payout.actions.process'))
                ->icon('heroicon-o-play')
                ->color('info')
                ->size('lg')
                ->requiresConfirmation()
                ->modalHeading(__('admin.payout.modal.process_title'))
                ->modalDescription(fn () => __('admin.payout.modal.process_desc_amount', [
                    'amount' => number_format((float) $this->record->net_payout, 3),
                    'owner' => $this->record->owner->name,
                ]))
                ->visible(fn () => $this->record->canProcess())
                ->action(function (): void {
                    if ($this->record->markAsProcessing(Auth::id())) {
                        Notification::make()
                            ->title(__('admin.payout.notifications.processing'))
                            ->body(__('admin.payout.notifications.processing_body', [
                                'number' => $this->record->payout_number,
                            ]))
                            ->success()
                            ->send();

                        $this->refreshFormData(['status', 'processed_at', 'processed_by']);
                    } else {
                        Notification::make()
                            ->title(__('admin.payout.notifications.process_failed'))
                            ->danger()
                            ->send();
                    }
                }),

            // Complete Action
            Actions\Action::make('complete')
                ->label(__('admin.payout.actions.complete'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->size('lg')
                ->form([
                    Forms\Components\Select::make('payment_method')
                        ->label(__('admin.payout.fields.payment_method'))
                        ->options([
                            'bank_transfer' => __('admin.payout.methods.bank_transfer'),
                            'cash' => __('admin.payout.methods.cash'),
                            'cheque' => __('admin.payout.methods.cheque'),
                            'other' => __('admin.payout.methods.other'),
                        ])
                        ->required()
                        ->native(false)
                        ->default($this->record->payment_method),

                    Forms\Components\TextInput::make('transaction_reference')
                        ->label(__('admin.payout.fields.transaction_reference'))
                        ->required()
                        ->maxLength(100)
                        ->default($this->record->transaction_reference)
                        ->placeholder('TXN-XXXX-XXXX'),
                ])
                ->modalHeading(__('admin.payout.modal.complete_title'))
                ->modalDescription(fn () => __('admin.payout.modal.complete_desc', [
                    'amount' => number_format((float) $this->record->net_payout, 3),
                ]))
                ->visible(fn () => $this->record->status === PayoutStatus::PROCESSING)
                ->action(function (array $data): void {
                    if ($this->record->markAsCompleted(
                        $data['transaction_reference'],
                        $data['payment_method']
                    )) {
                        Notification::make()
                            ->title(__('admin.payout.notifications.completed'))
                            ->body(__('admin.payout.notifications.completed_body', [
                                'amount' => number_format((float) $this->record->net_payout, 3),
                                'owner' => $this->record->owner->name,
                            ]))
                            ->success()
                            ->duration(5000)
                            ->send();

                        $this->refreshFormData([
                            'status',
                            'completed_at',
                            'payment_method',
                            'transaction_reference',
                        ]);
                    }
                }),

            // Mark Failed Action
            Actions\Action::make('fail')
                ->label(__('admin.payout.actions.fail'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    Forms\Components\Textarea::make('failure_reason')
                        ->label(__('admin.payout.fields.failure_reason'))
                        ->required()
                        ->rows(3)
                        ->maxLength(500)
                        ->placeholder(__('admin.payout.failure_placeholder')),
                ])
                ->modalHeading(__('admin.payout.modal.fail_title'))
                ->visible(fn () => $this->record->status === PayoutStatus::PROCESSING)
                ->action(function (array $data): void {
                    if ($this->record->markAsFailed($data['failure_reason'])) {
                        Notification::make()
                            ->title(__('admin.payout.notifications.failed'))
                            ->body(__('admin.payout.notifications.failed_body'))
                            ->warning()
                            ->send();

                        $this->refreshFormData(['status', 'failed_at', 'failure_reason']);
                    }
                }),

            // Hold Action
            Actions\Action::make('hold')
                ->label(__('admin.payout.actions.hold'))
                ->icon('heroicon-o-pause-circle')
                ->color('warning')
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label(__('admin.payout.fields.hold_reason'))
                        ->rows(2)
                        ->maxLength(500)
                        ->placeholder(__('admin.payout.hold_placeholder')),
                ])
                ->modalHeading(__('admin.payout.modal.hold_title'))
                ->visible(fn () => $this->record->canCancel())
                ->action(function (array $data): void {
                    if ($this->record->putOnHold($data['reason'] ?? null)) {
                        Notification::make()
                            ->title(__('admin.payout.notifications.on_hold'))
                            ->body(__('admin.payout.notifications.on_hold_body'))
                            ->warning()
                            ->send();

                        $this->refreshFormData(['status', 'notes']);
                    }
                }),

            // Cancel Action
            Actions\Action::make('cancel')
                ->label(__('admin.payout.actions.cancel'))
                ->icon('heroicon-o-no-symbol')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading(__('admin.payout.modal.cancel_title'))
                ->modalDescription(__('admin.payout.modal.cancel_desc'))
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label(__('admin.payout.fields.cancel_reason'))
                        ->rows(2)
                        ->maxLength(500),
                ])
                ->visible(fn () => $this->record->canCancel())
                ->action(function (array $data): void {
                    if ($this->record->cancel($data['reason'] ?? null)) {
                        Notification::make()
                            ->title(__('admin.payout.notifications.cancelled'))
                            ->body(__('admin.payout.notifications.cancelled_body'))
                            ->send();

                        $this->redirect($this->getResource()::getUrl('index'));
                    }
                }),

            // Print Receipt Action
            Actions\Action::make('print_receipt')
                ->label(__('admin.payout.actions.print'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->visible(fn () => $this->record->status === PayoutStatus::COMPLETED)
                ->url(fn () => route('admin.payout.receipt', $this->record))
                ->openUrlInNewTab(),

            // Delete Action
            Actions\DeleteAction::make()
                ->visible(fn () => !$this->record->status->isTerminal()),
        ];
    }

    /**
     * Get the footer widgets for this page.
     *
     * @return array
     */
    protected function getFooterWidgets(): array
    {
        return [
            // Could add related bookings widget here
        ];
    }
}
