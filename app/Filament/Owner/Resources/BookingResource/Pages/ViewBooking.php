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
        return __('Booking: :number', ['number' => $this->record->booking_number]);
    }

    /**
     * Get the page subtitle/description.
     */
    public function getSubheading(): string|Htmlable|null
    {
        $hall = $this->record->hall;
        $hallName = $hall?->name[app()->getLocale()] ?? $hall?->name['en'] ?? 'N/A';

        return $hallName . ' • ' . $this->record->booking_date->format('l, d M Y') . ' • ' . 
            ucfirst(str_replace('_', ' ', $this->record->time_slot));
    }

    /**
     * Get the header actions for this page.
     */
    protected function getHeaderActions(): array
    {
        return [
            // Approve Action
            Actions\Action::make('approve')
                ->label(__('Approve Booking'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->size('lg')
                ->requiresConfirmation()
                ->modalHeading(__('Approve Booking'))
                ->modalDescription(__('Are you sure you want to approve this booking? The customer will receive a confirmation notification.'))
                ->modalSubmitActionLabel(__('Yes, Approve'))
                ->visible(fn(): bool => 
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
                        ->title(__('Booking Approved'))
                        ->body(__('The booking has been approved and the customer has been notified.'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'confirmed_at']);
                }),

            // Reject Action
            Actions\Action::make('reject')
                ->label(__('Reject Booking'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->size('lg')
                ->requiresConfirmation()
                ->modalHeading(__('Reject Booking'))
                ->modalDescription(__('Please provide a reason for rejecting this booking. The customer will be notified.'))
                ->form([
                    Forms\Components\Textarea::make('rejection_reason')
                        ->label(__('Reason for Rejection'))
                        ->required()
                        ->maxLength(500)
                        ->placeholder(__('e.g., Hall unavailable due to maintenance, double booking error, etc.'))
                        ->helperText(__('This reason will be shared with the customer.')),
                ])
                ->visible(fn(): bool => 
                    $this->record->status === 'pending' && 
                    $this->record->hall?->requires_approval
                )
                ->action(function (array $data): void {
                    $this->record->update([
                        'status' => 'cancelled',
                        'cancelled_at' => now(),
                        'cancellation_reason' => __('Rejected by hall owner: ') . $data['rejection_reason'],
                    ]);

                    // TODO: Dispatch event for customer notification
                    // event(new BookingRejected($this->record));

                    Notification::make()
                        ->title(__('Booking Rejected'))
                        ->body(__('The booking has been rejected and the customer has been notified.'))
                        ->warning()
                        ->send();

                    $this->refreshFormData(['status', 'cancelled_at', 'cancellation_reason']);
                }),

            // Record Balance Payment Action
            Actions\Action::make('record_balance')
                ->label(__('Record Balance Payment'))
                ->icon('heroicon-o-banknotes')
                ->color('info')
                ->size('lg')
                ->modalHeading(__('Record Balance Payment'))
                ->modalDescription(__('Record that the remaining balance has been received from the customer.'))
                ->form([
                    Forms\Components\Section::make(__('Payment Details'))
                        ->schema([
                            Forms\Components\Placeholder::make('balance_summary')
                                ->label(__('Balance Summary'))
                                ->content(fn(): string => 
                                    __('Total: OMR :total | Advance Paid: OMR :advance | Balance Due: OMR :balance', [
                                        'total' => number_format((float) $this->record->total_amount, 3),
                                        'advance' => number_format((float) $this->record->advance_amount, 3),
                                        'balance' => number_format((float) $this->record->balance_due, 3),
                                    ])
                                ),

                            Forms\Components\TextInput::make('amount_received')
                                ->label(__('Amount Received'))
                                ->prefix('OMR')
                                ->numeric()
                                ->default(fn(): string => number_format((float) $this->record->balance_due, 3, '.', ''))
                                ->required()
                                ->minValue(0.001)
                                ->step(0.001)
                                ->helperText(__('Enter the actual amount received from customer')),

                            Forms\Components\Select::make('payment_method')
                                ->label(__('Payment Method'))
                                ->options([
                                    'cash' => __('Cash'),
                                    'bank_transfer' => __('Bank Transfer'),
                                    'card' => __('Card (POS Machine)'),
                                    'cheque' => __('Cheque'),
                                ])
                                ->required()
                                ->native(false),

                            Forms\Components\TextInput::make('reference')
                                ->label(__('Receipt/Reference Number'))
                                ->placeholder(__('e.g., Receipt #12345 or Transfer Ref'))
                                ->maxLength(100),

                            Forms\Components\DateTimePicker::make('received_at')
                                ->label(__('Received Date & Time'))
                                ->default(now())
                                ->required()
                                ->maxDate(now()),

                            Forms\Components\Textarea::make('notes')
                                ->label(__('Additional Notes'))
                                ->placeholder(__('Any relevant notes about this payment...'))
                                ->maxLength(500)
                                ->rows(2),
                        ])
                        ->columns(2),
                ])
                ->visible(fn(): bool => 
                    $this->record->payment_type === 'advance' && 
                    (float) ($this->record->balance_due ?? 0) > 0 &&
                    $this->record->balance_paid_at === null &&
                    in_array($this->record->status, ['confirmed', 'pending'])
                )
                ->action(function (array $data): void {
                    $notes = sprintf(
                        __("Balance Payment Recorded:\n- Amount: OMR %s\n- Method: %s\n- Reference: %s\n- Received: %s"),
                        number_format((float) $data['amount_received'], 3),
                        $data['payment_method'],
                        $data['reference'] ?? 'N/A',
                        $data['received_at']
                    );

                    if (!empty($data['notes'])) {
                        $notes .= "\n- Notes: " . $data['notes'];
                    }

                    $this->record->update([
                        'balance_paid_at' => $data['received_at'],
                        'balance_payment_method' => $data['payment_method'],
                        'balance_payment_reference' => $data['reference'] ?? null,
                        'payment_status' => 'paid',
                        'admin_notes' => ($this->record->admin_notes ? $this->record->admin_notes . "\n\n" : '') . $notes,
                    ]);

                    Notification::make()
                        ->title(__('Balance Payment Recorded'))
                        ->body(__('The balance payment of OMR :amount has been recorded successfully.', [
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
                    ->label(__('Call Customer'))
                    ->icon('heroicon-o-phone')
                    ->url(fn(): string => "tel:{$this->record->customer_phone}")
                    ->openUrlInNewTab(),

                Actions\Action::make('email')
                    ->label(__('Send Email'))
                    ->icon('heroicon-o-envelope')
                    ->url(fn(): string => "mailto:{$this->record->customer_email}?subject=" . 
                        urlencode(__('Regarding Booking :number', ['number' => $this->record->booking_number])))
                    ->openUrlInNewTab(),

                Actions\Action::make('whatsapp')
                    ->label(__('WhatsApp Message'))
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->url(fn(): string => "https://wa.me/" . preg_replace('/[^0-9]/', '', $this->record->customer_phone) . 
                        "?text=" . urlencode(__('Hello! Regarding your booking :number at :hall on :date.', [
                            'number' => $this->record->booking_number,
                            'hall' => $this->record->hall?->name[app()->getLocale()] ?? $this->record->hall?->name['en'] ?? 'our hall',
                            'date' => $this->record->booking_date->format('d M Y'),
                        ])))
                    ->openUrlInNewTab(),
            ])
                ->label(__('Contact Customer'))
                ->icon('heroicon-o-chat-bubble-bottom-center-text')
                ->color('gray')
                ->button(),

            // Print/Download Invoice
            Actions\Action::make('download_invoice')
                ->label(__('Download Invoice'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->visible(fn(): bool => !empty($this->record->invoice_path))
                ->url(fn(): string => asset('storage/' . $this->record->invoice_path))
                ->openUrlInNewTab(),

            // Back to List
            Actions\Action::make('back')
                ->label(__('Back to Bookings'))
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
