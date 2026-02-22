<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\BookingResource\Pages;

use App\Filament\Owner\Resources\BookingResource;
use App\Models\Booking;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

/**
 * ViewBooking Page for Owner Panel
 *
 * Displays comprehensive booking details with contextual actions
 * for approve/reject (if requires_approval) and recording balance payments.
 */
class ViewBooking extends ViewRecord
{
    /**
     * The resource class this page belongs to.
     */
    protected static string $resource = BookingResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string|Htmlable
    {
        return __('pages/view-booking.title', ['number' => $this->record->booking_number]);
    }

    /**
     * Get the page subtitle/description.
     */
    public function getSubheading(): string|Htmlable|null
    {
        $hall = $this->record->hall;
        $hallName = $hall?->name[app()->getLocale()] ?? $hall?->name['en'] ?? __('common.na');

        return __('pages/view-booking.subheading', [
            'hall' => $hallName,
            'date' => $this->record->booking_date->format('l, d M Y'),
            'time_slot' => __("common.time_slots.{$this->record->time_slot}"),
        ]);
    }

    /**
     * Get the header actions for this page.
     */
    protected function getHeaderActions(): array
    {
        return [
            // Approve Action
            Actions\Action::make('approve')
                ->label(__('pages/view-booking.actions.approve.label'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->size('lg')
                ->requiresConfirmation()
                ->modalHeading(__('pages/view-booking.actions.approve.modal_heading'))
                ->modalDescription(__('pages/view-booking.actions.approve.modal_description'))
                ->modalSubmitActionLabel(__('pages/view-booking.actions.approve.submit_label'))
                ->visible(
                    fn(): bool =>
                    $this->record->status === 'pending' &&
                        $this->record->hall?->requires_approval
                )
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'confirmed',
                        'confirmed_at' => now(),
                    ]);

                    // TODO: Dispatch event for customer notification
                    // event(new BookingApproved($this->record));

                    Notification::make()
                        ->title(__('pages/view-booking.notifications.approved.title'))
                        ->body(__('pages/view-booking.notifications.approved.body'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'confirmed_at']);
                }),

            // Reject Action
            Actions\Action::make('reject')
                ->label(__('pages/view-booking.actions.reject.label'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->size('lg')
                ->requiresConfirmation()
                ->modalHeading(__('pages/view-booking.actions.reject.modal_heading'))
                ->modalDescription(__('pages/view-booking.actions.reject.modal_description'))
                ->form([
                    Forms\Components\Textarea::make('rejection_reason')
                        ->label(__('pages/view-booking.actions.reject.reason_label'))
                        ->required()
                        ->maxLength(500)
                        ->placeholder(__('pages/view-booking.actions.reject.reason_placeholder'))
                        ->helperText(__('pages/view-booking.actions.reject.reason_helper')),
                ])
                ->visible(
                    fn(): bool =>
                    $this->record->status === 'pending' &&
                        $this->record->hall?->requires_approval
                )
                ->action(function (array $data): void {
                    $this->record->update([
                        'status' => 'cancelled',
                        'cancelled_at' => now(),
                        'cancellation_reason' => __('pages/view-booking.actions.reject.reason_prefix') . $data['rejection_reason'],
                    ]);

                    // TODO: Dispatch event for customer notification
                    // event(new BookingRejected($this->record));

                    Notification::make()
                        ->title(__('pages/view-booking.notifications.rejected.title'))
                        ->body(__('pages/view-booking.notifications.rejected.body'))
                        ->warning()
                        ->send();

                    $this->refreshFormData(['status', 'cancelled_at', 'cancellation_reason']);
                }),

            // Record Balance Payment Action
            Actions\Action::make('record_balance')
                ->label(__('pages/view-booking.actions.record_balance.label'))
                ->icon('heroicon-o-banknotes')
                ->color('info')
                ->size('lg')
                ->modalHeading(__('pages/view-booking.actions.record_balance.modal_heading'))
                ->modalDescription(__('pages/view-booking.actions.record_balance.modal_description'))
                ->form([
                    Forms\Components\Section::make(__('pages/view-booking.actions.record_balance.section_title'))
                        ->schema([
                            Forms\Components\Placeholder::make('balance_summary')
                                ->label(__('pages/view-booking.actions.record_balance.balance_summary_label'))
                                ->content(
                                    fn(): string =>
                                    __('pages/view-booking.actions.record_balance.balance_summary_content', [
                                        'total' => number_format((float) $this->record->total_amount, 3),
                                        'advance' => number_format((float) $this->record->advance_amount, 3),
                                        'balance' => number_format((float) $this->record->balance_due, 3),
                                    ])
                                ),

                            Forms\Components\TextInput::make('amount_received')
                                ->label(__('pages/view-booking.actions.record_balance.amount_received_label'))
                                ->prefix('OMR')
                                ->numeric()
                                ->default(fn(): string => number_format((float) $this->record->balance_due, 3, '.', ''))
                                ->required()
                                ->minValue(0.001)
                                ->step(0.001)
                                ->helperText(__('pages/view-booking.actions.record_balance.amount_received_helper')),

                            Forms\Components\Select::make('payment_method')
                                ->label(__('pages/view-booking.actions.record_balance.payment_method_label'))
                                ->options([
                                    'cash' => __('common.payment_methods.cash'),
                                    'bank_transfer' => __('common.payment_methods.bank_transfer'),
                                    'card' => __('pages/view-booking.actions.record_balance.payment_methods.card'),
                                    'cheque' => __('pages/view-booking.actions.record_balance.payment_methods.cheque'),
                                ])
                                ->required()
                                ->native(false),

                            Forms\Components\TextInput::make('reference')
                                ->label(__('pages/view-booking.actions.record_balance.reference_label'))
                                ->placeholder(__('pages/view-booking.actions.record_balance.reference_placeholder'))
                                ->maxLength(100),

                            Forms\Components\DateTimePicker::make('received_at')
                                ->label(__('pages/view-booking.actions.record_balance.received_at_label'))
                                ->default(now())
                                ->required()
                                ->maxDate(now()),

                            Forms\Components\Textarea::make('notes')
                                ->label(__('pages/view-booking.actions.record_balance.notes_label'))
                                ->placeholder(__('pages/view-booking.actions.record_balance.notes_placeholder'))
                                ->maxLength(500)
                                ->rows(2),
                        ])
                        ->columns(2),
                ])
                ->visible(
                    fn(): bool =>
                    $this->record->payment_type === 'advance' &&
                        (float) ($this->record->balance_due ?? 0) > 0 &&
                        $this->record->balance_paid_at === null &&
                        in_array($this->record->status, ['confirmed', 'pending'])
                )
                ->action(function (array $data): void {
                    $notes = sprintf(
                        __("pages/view-booking.actions.record_balance.notes_format"),
                        number_format((float) $data['amount_received'], 3),
                        $data['payment_method'],
                        $data['reference'] ?? 'N/A',
                        $data['received_at']
                    );

                    if (!empty($data['notes'])) {
                        $notes .= "\n- " . __('pages/view-booking.actions.record_balance.notes_additional') . " " . $data['notes'];
                    }

                    $this->record->update([
                        'balance_paid_at' => $data['received_at'],
                        'balance_payment_method' => $data['payment_method'],
                        'balance_payment_reference' => $data['reference'] ?? null,
                        'payment_status' => 'paid',
                        'admin_notes' => ($this->record->admin_notes ? $this->record->admin_notes . "\n\n" : '') . $notes,
                    ]);

                    Notification::make()
                        ->title(__('pages/view-booking.notifications.balance_recorded.title'))
                        ->body(__('pages/view-booking.notifications.balance_recorded.body', [
                            'amount' => number_format((float) $data['amount_received'], 3)
                        ]))
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'balance_paid_at',
                        'balance_payment_method',
                        'balance_payment_reference',
                        'payment_status',
                        'admin_notes',
                    ]);
                }),

            // Contact Customer Action Group
            Actions\ActionGroup::make([
                Actions\Action::make('call')
                    ->label(__('pages/view-booking.actions.contact.call'))
                    ->icon('heroicon-o-phone')
                    ->url(fn(): string => "tel:{$this->record->customer_phone}")
                    ->openUrlInNewTab(),

                Actions\Action::make('email')
                    ->label(__('pages/view-booking.actions.contact.email'))
                    ->icon('heroicon-o-envelope')
                    ->url(fn(): string => "mailto:{$this->record->customer_email}?subject=" .
                        urlencode(__('pages/view-booking.actions.contact.email_subject', ['number' => $this->record->booking_number])))
                    ->openUrlInNewTab(),

                Actions\Action::make('whatsapp')
                    ->label(__('pages/view-booking.actions.contact.whatsapp'))
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->url(fn(): string => "https://api.whatsapp.com/send?phone=" . preg_replace('/[^0-9]/', '', $this->record->customer_phone) .
                        "?text=" . urlencode(__('pages/view-booking.actions.contact.whatsapp_message', [
                            'number' => $this->record->booking_number,
                            'hall' => $this->record->hall?->name[app()->getLocale()] ?? $this->record->hall?->name['en'] ?? __('common.hall'),
                            'date' => $this->record->booking_date->format('d M Y'),
                        ])))
                    ->openUrlInNewTab(),
            ])
                ->label(__('pages/view-booking.actions.contact.group_label'))
                ->icon('heroicon-o-chat-bubble-bottom-center-text')
                ->color('gray')
                ->button(),

            // Print/Download Invoice
            Actions\Action::make('download_invoice')
                ->label(__('pages/view-booking.actions.download_invoice'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->visible(fn(): bool => !empty($this->record->invoice_path))
                ->url(fn(): string => asset('storage/' . $this->record->invoice_path))
                ->openUrlInNewTab(),

            // Back to List
            Actions\Action::make('back')
                ->label(__('pages/view-booking.actions.back'))
                ->url(BookingResource::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    /**
     * Get the footer widgets for this page.
     */
    protected function getFooterWidgets(): array
    {
        return [];
    }

    /**
     * Get the relation managers for this page.
     */
    public function getRelationManagers(): array
    {
        return [
            BookingResource\RelationManagers\PaymentsRelationManager::class,
        ];
    }
}
