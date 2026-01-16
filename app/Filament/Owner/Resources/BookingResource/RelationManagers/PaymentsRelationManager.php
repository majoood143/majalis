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
        return __('Payment History');
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
                    ->label(__('Reference'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('Reference copied'))
                    ->weight(FontWeight::Medium)
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small),

                // Amount
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money('OMR')
                    ->sortable()
                    ->weight(FontWeight::Bold),

                // Payment Method
                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('Method'))
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'online' => __('Online'),
                        'cash' => __('Cash'),
                        'bank_transfer' => __('Bank Transfer'),
                        'card' => __('Card'),
                        default => ucfirst($state ?? 'N/A'),
                    })
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
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => __('Pending'),
                        'processing' => __('Processing'),
                        'paid' => __('Paid'),
                        'failed' => __('Failed'),
                        'cancelled' => __('Cancelled'),
                        'refunded' => __('Refunded'),
                        default => ucfirst($state),
                    })
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
                    ->label(__('Paid At'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder(__('Not paid'))
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
                    ->label(__('Refund'))
                    ->money('OMR')
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true)
                    // Use placeholder for null/empty values instead of visible()
                    ->placeholder(__('N/A'))
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
                    ->label(__('Transaction ID'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder(__('N/A')),

                // Created At
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending' => __('Pending'),
                        'processing' => __('Processing'),
                        'paid' => __('Paid'),
                        'failed' => __('Failed'),
                        'cancelled' => __('Cancelled'),
                        'refunded' => __('Refunded'),
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label(__('Payment Method'))
                    ->options([
                        'online' => __('Online'),
                        'cash' => __('Cash'),
                        'bank_transfer' => __('Bank Transfer'),
                        'card' => __('Card'),
                    ]),
            ])
            ->headerActions([
                // Owners cannot add payments
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->modalHeading(fn($record) => __('Payment: :ref', ['ref' => $record->payment_reference]))
                    ->modalWidth('lg'),
            ])
            ->bulkActions([
                // No bulk actions for owners
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('No payments recorded'))
            ->emptyStateDescription(__('Payment records will appear here once processed.'))
            ->emptyStateIcon('heroicon-o-credit-card');
    }
}
