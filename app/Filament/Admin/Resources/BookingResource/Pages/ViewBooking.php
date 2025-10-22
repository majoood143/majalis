<?php

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('confirm')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->confirm();
                    $this->record->refresh();

                    \Filament\Notifications\Notification::make()
                        ->title('Booking confirmed successfully')
                        ->success()
                        ->send();
                })
                ->visible(fn() => $this->record->status->value === 'pending'),

            Actions\Action::make('cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Cancellation Reason')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->record->cancel($data['reason']);
                    $this->record->refresh();

                    \Filament\Notifications\Notification::make()
                        ->title('Booking cancelled successfully')
                        ->success()
                        ->send();
                })
                ->visible(fn() => in_array($this->record->status->value, ['pending', 'confirmed'])),

            Actions\Action::make('complete')
                ->icon('heroicon-o-check-badge')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->complete();
                    $this->record->refresh();

                    \Filament\Notifications\Notification::make()
                        ->title('Booking completed successfully')
                        ->success()
                        ->send();
                })
                ->visible(fn() => $this->record->status->value === 'confirmed' &&
                    $this->record->booking_date->isPast()),

            Actions\Action::make('downloadInvoice')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    if ($this->record->invoice_path) {
                        return response()->download(storage_path('app/' . $this->record->invoice_path));
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Invoice not available')
                        ->warning()
                        ->send();
                })
                ->visible(fn() => !empty($this->record->invoice_path)),

            Actions\Action::make('generateInvoice')
                ->icon('heroicon-o-document-plus')
                ->color('gray')
                ->action(function () {
                    $pdfService = app(\App\Services\PDFService::class);
                    $pdfService->generateBookingInvoice($this->record);
                    $this->record->refresh();

                    \Filament\Notifications\Notification::make()
                        ->title('Invoice generated successfully')
                        ->success()
                        ->send();
                })
                ->visible(fn() => empty($this->record->invoice_path) &&
                    $this->record->status->value === 'confirmed'),

            Actions\Action::make('sendReminder')
                ->icon('heroicon-o-bell')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $notificationService = app(\App\Services\NotificationService::class);
                    $notificationService->sendBookingReminderNotification($this->record);

                    \Filament\Notifications\Notification::make()
                        ->title('Reminder sent successfully')
                        ->success()
                        ->send();
                })
                ->visible(fn() => $this->record->status->value === 'confirmed' &&
                    $this->record->booking_date->isFuture()),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Booking Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('booking_number')
                                    ->label('Booking Number')
                                    ->copyable()
                                    ->badge()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('status')
                                    ->badge(),

                                Infolists\Components\TextEntry::make('payment_status')
                                    ->badge(),
                            ]),
                    ]),

                Infolists\Components\Section::make('Hall & Date Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label('Hall')
                                    ->formatStateUsing(fn($record) => $record->hall->name),

                                Infolists\Components\TextEntry::make('hall.city.name')
                                    ->label('Location')
                                    ->formatStateUsing(fn($record) => $record->hall->city->name . ', ' . $record->hall->city->region->name),

                                Infolists\Components\TextEntry::make('booking_date')
                                    ->date('d M Y')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('time_slot')
                                    ->badge()
                                    ->formatStateUsing(fn(string $state): string => ucfirst(str_replace('_', ' ', $state))),

                                Infolists\Components\TextEntry::make('number_of_guests')
                                    ->suffix(' guests')
                                    ->icon('heroicon-o-users'),

                                Infolists\Components\TextEntry::make('event_type')
                                    ->formatStateUsing(fn(?string $state): string => $state ? ucfirst($state) : '-'),
                            ]),
                    ])->columns(2),

                Infolists\Components\Section::make('Customer Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('customer_name')
                                    ->icon('heroicon-o-user'),

                                Infolists\Components\TextEntry::make('customer_email')
                                    ->copyable()
                                    ->icon('heroicon-o-envelope'),

                                Infolists\Components\TextEntry::make('customer_phone')
                                    ->copyable()
                                    ->icon('heroicon-o-phone'),
                            ]),

                        Infolists\Components\TextEntry::make('customer_notes')
                            ->columnSpanFull()
                            ->placeholder('No notes provided'),
                    ]),

                Infolists\Components\Section::make('Pricing Breakdown')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall_price')
                                    ->money('OMR')
                                    ->label('Hall Price'),

                                Infolists\Components\TextEntry::make('services_price')
                                    ->money('OMR')
                                    ->label('Services'),

                                Infolists\Components\TextEntry::make('subtotal')
                                    ->money('OMR')
                                    ->weight('bold'),

                                Infolists\Components\TextEntry::make('commission_amount')
                                    ->money('OMR')
                                    ->label('Platform Fee')
                                    ->color('warning'),

                                Infolists\Components\TextEntry::make('total_amount')
                                    ->money('OMR')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('owner_payout')
                                    ->money('OMR')
                                    ->label('Owner Payout')
                                    ->color('info'),
                            ]),
                    ])->columns(3),

                Infolists\Components\Section::make('Extra Services')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('extraServices')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('pivot.service_name')
                                    ->label('Service'),
                                Infolists\Components\TextEntry::make('pivot.unit_price')
                                    ->money('OMR')
                                    ->label('Unit Price'),
                                Infolists\Components\TextEntry::make('pivot.quantity')
                                    ->label('Quantity'),
                                Infolists\Components\TextEntry::make('pivot.total_price')
                                    ->money('OMR')
                                    ->label('Total'),
                            ])
                            ->columns(4),
                    ])
                    ->visible(fn() => $this->record->extraServices->count() > 0),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->dateTime(),

                                Infolists\Components\TextEntry::make('confirmed_at')
                                    ->dateTime()
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('completed_at')
                                    ->dateTime()
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('cancelled_at')
                                    ->dateTime()
                                    ->placeholder('-')
                                    ->visible(fn() => $this->record->cancelled_at),
                            ]),
                    ])
                    ->collapsed(),

                Infolists\Components\Section::make('Cancellation Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('cancellation_reason')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('refund_amount')
                            ->money('OMR'),
                    ])
                    ->visible(fn() => $this->record->status->value === 'cancelled'),

                Infolists\Components\Section::make('Admin Notes')
                    ->schema([
                        Infolists\Components\TextEntry::make('admin_notes')
                            ->columnSpanFull()
                            ->placeholder('No admin notes'),
                    ])
                    ->collapsed()
                    ->visible(fn() => !empty($this->record->admin_notes)),
            ]);
    }
}
