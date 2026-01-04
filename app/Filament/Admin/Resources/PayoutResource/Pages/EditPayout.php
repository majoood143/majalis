<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PayoutResource\Pages;

use App\Enums\PayoutStatus;
use App\Filament\Admin\Resources\PayoutResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

/**
 * EditPayout Page
 *
 * Handles editing of existing owner payouts.
 * Includes workflow actions for status changes.
 *
 * @package App\Filament\Admin\Resources\PayoutResource\Pages
 */
class EditPayout extends EditRecord
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
            // View Action
            Actions\ViewAction::make(),

            // Process Action
            Actions\Action::make('process')
                ->label(__('admin.payout.actions.process'))
                ->icon('heroicon-o-play')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->canProcess())
                ->action(function (): void {
                    if ($this->record->markAsProcessing(Auth::id())) {
                        Notification::make()
                            ->title(__('admin.payout.notifications.processing'))
                            ->success()
                            ->send();
                        
                        $this->refreshFormData(['status', 'processed_at', 'processed_by']);
                    }
                }),

            // Complete Action
            Actions\Action::make('complete')
                ->label(__('admin.payout.actions.complete'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('payment_method')
                        ->label(__('admin.payout.fields.payment_method'))
                        ->options([
                            'bank_transfer' => __('admin.payout.methods.bank_transfer'),
                            'cash' => __('admin.payout.methods.cash'),
                            'cheque' => __('admin.payout.methods.cheque'),
                            'other' => __('admin.payout.methods.other'),
                        ])
                        ->required()
                        ->default($this->record->payment_method),

                    \Filament\Forms\Components\TextInput::make('transaction_reference')
                        ->label(__('admin.payout.fields.transaction_reference'))
                        ->required()
                        ->maxLength(100)
                        ->default($this->record->transaction_reference),
                ])
                ->visible(fn () => $this->record->status === PayoutStatus::PROCESSING)
                ->action(function (array $data): void {
                    if ($this->record->markAsCompleted(
                        $data['transaction_reference'],
                        $data['payment_method']
                    )) {
                        Notification::make()
                            ->title(__('admin.payout.notifications.completed'))
                            ->success()
                            ->send();
                        
                        $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                    }
                }),

            // Hold Action
            Actions\Action::make('hold')
                ->label(__('admin.payout.actions.hold'))
                ->icon('heroicon-o-pause-circle')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label(__('admin.payout.fields.hold_reason'))
                        ->rows(2),
                ])
                ->visible(fn () => $this->record->canCancel())
                ->action(function (array $data): void {
                    if ($this->record->putOnHold($data['reason'] ?? null)) {
                        Notification::make()
                            ->title(__('admin.payout.notifications.on_hold'))
                            ->warning()
                            ->send();
                        
                        $this->refreshFormData(['status', 'notes']);
                    }
                }),

            // Cancel Action
            Actions\Action::make('cancel')
                ->label(__('admin.payout.actions.cancel'))
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label(__('admin.payout.fields.cancel_reason'))
                        ->rows(2),
                ])
                ->visible(fn () => $this->record->canCancel())
                ->action(function (array $data): void {
                    if ($this->record->cancel($data['reason'] ?? null)) {
                        Notification::make()
                            ->title(__('admin.payout.notifications.cancelled'))
                            ->send();
                        
                        $this->redirect($this->getResource()::getUrl('index'));
                    }
                }),

            // Delete Action
            Actions\DeleteAction::make()
                ->visible(fn () => !$this->record->status->isTerminal()),
        ];
    }

    /**
     * Mutate form data before saving the record.
     *
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalculate net payout
        $gross = (float) ($data['gross_revenue'] ?? 0);
        $commission = (float) ($data['commission_amount'] ?? 0);
        $adjustments = (float) ($data['adjustments'] ?? 0);

        $data['net_payout'] = max(0, $gross - $commission + $adjustments);

        // Calculate commission rate
        if ($gross > 0) {
            $data['commission_rate'] = ($commission / $gross) * 100;
        }

        return $data;
    }

    /**
     * Handle actions after saving the record.
     *
     * @return void
     */
    protected function afterSave(): void
    {
        Notification::make()
            ->title(__('admin.payout.notifications.updated'))
            ->success()
            ->send();
    }

    /**
     * Get the redirect URL after saving the record.
     *
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
