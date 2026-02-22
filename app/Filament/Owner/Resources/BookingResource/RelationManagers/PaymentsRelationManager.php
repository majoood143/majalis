<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\BookingResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * PaymentsRelationManager for Owner Panel
 *
 * Displays payment history for a booking.
 * Owners can only view payments, not modify them.
 * Commission details are hidden from owners.
 */
class PaymentsRelationManager extends RelationManager
{
    /**
     * The relationship name.
     */
    protected static string $relationship = 'payments';

    /**
     * The title for this relation manager.
     */
    protected static ?string $title = null;

    /**
     * Get the title.
     */
    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('relation-managers.payments.title');
    }

    /**
     * The icon for the navigation.
     */
    protected static ?string $icon = 'heroicon-o-credit-card';

    /**
     * Determine if the user can create records.
     */
    public function canCreate(): bool
    {
        return false;
    }

    /**
     * Determine if the user can edit records.
     */
    public function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    /**
     * Determine if the user can delete records.
     */
    public function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    /**
     * Define the table schema.
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payment_reference')
            ->columns([
                // Payment Reference
                Tables\Columns\TextColumn::make('payment_reference')
                    ->label(__('relation-managers.payments.columns.reference'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('relation-managers.payments.messages.reference_copied'))
                    ->weight(FontWeight::Medium)
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small),

                // Amount
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('relation-managers.payments.columns.amount'))
                    ->money('OMR')
                    ->sortable()
                    ->weight(FontWeight::Bold),

                // Payment Method
                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('relation-managers.payments.columns.method'))
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => $state
                        ? __("common.payment_methods.{$state}")
                        : __('common.na'))
                    ->color(fn(?string $state): string => match ($state) {
                        'online' => 'info',
                        'cash' => 'success',
                        'bank_transfer' => 'warning',
                        'card' => 'primary',
                        default => 'gray',
                    })
                    ->icon(fn(?string $state): string => match ($state) {
                        'online' => 'heroicon-m-globe-alt',
                        'cash' => 'heroicon-m-banknotes',
                        'bank_transfer' => 'heroicon-m-building-library',
                        'card' => 'heroicon-m-credit-card',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                // Status
                Tables\Columns\TextColumn::make('status')
                    ->label(__('relation-managers.payments.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => __("common.payment_status.{$state}"))
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'processing' => 'info',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        'refunded' => 'purple',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'paid' => 'heroicon-m-check-circle',
                        'pending' => 'heroicon-m-clock',
                        'processing' => 'heroicon-m-arrow-path',
                        'failed' => 'heroicon-m-x-circle',
                        'cancelled' => 'heroicon-m-minus-circle',
                        'refunded' => 'heroicon-m-arrow-uturn-left',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                // Paid At
                Tables\Columns\TextColumn::make('paid_at')
                    ->label(__('relation-managers.payments.columns.paid_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder(__('common.not_paid'))
                    ->toggleable(),

                /**
                 * Refund Amount Column
                 *
                 * FIX: The visible() method on columns is evaluated at the TABLE level,
                 * not per-row. The $record parameter is null during column visibility
                 * evaluation because visibility determines if the entire column shows.
                 *
                 * Solution: Use formatStateUsing() to conditionally display content
                 * based on the record's status, or use placeholder() for null values.
                 *
                 * @see https://filamentphp.com/docs/3.x/tables/columns/getting-started#conditional-formatting
                 */
                Tables\Columns\TextColumn::make('refund_amount')
                    ->label(__('relation-managers.payments.columns.refund'))
                    ->money('OMR')
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true)
                    // Use placeholder for null/empty values instead of visible()
                    ->placeholder(__('common.na'))
                    // Alternative: Only show formatted value when status is refunded
                    ->formatStateUsing(function ($state, $record): ?string {
                        // Safely check if record exists and has refunded status
                        if ($record === null || $record->status !== 'refunded') {
                            return null; // Will show placeholder
                        }

                        // Return null to let money() formatter handle it
                        // or return formatted string if needed
                        return $state;
                    }),

                // Transaction ID (external)
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label(__('relation-managers.payments.columns.transaction_id'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder(__('common.na')),

                // Created At
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('relation-managers.payments.columns.created'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('relation-managers.payments.filters.status'))
                    ->options([
                        'pending' => __('common.payment_status.pending'),
                        'processing' => __('common.payment_status.processing'),
                        'paid' => __('common.payment_status.paid'),
                        'failed' => __('common.payment_status.failed'),
                        'cancelled' => __('common.payment_status.cancelled'),
                        'refunded' => __('common.payment_status.refunded'),
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label(__('relation-managers.payments.filters.payment_method'))
                    ->options([
                        'online' => __('common.payment_methods.online'),
                        'cash' => __('common.payment_methods.cash'),
                        'bank_transfer' => __('common.payment_methods.bank_transfer'),
                        'card' => __('common.payment_methods.card'),
                    ]),
            ])
            ->headerActions([
                // Owners cannot add payments
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->modalHeading(fn($record) => __('relation-managers.payments.messages.view_payment', ['ref' => $record->payment_reference]))
                    ->modalWidth('lg'),
            ])
            ->bulkActions([
                // No bulk actions for owners
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('relation-managers.payments.empty_state.heading'))
            ->emptyStateDescription(__('relation-managers.payments.empty_state.description'))
            ->emptyStateIcon('heroicon-o-credit-card');
    }
}
