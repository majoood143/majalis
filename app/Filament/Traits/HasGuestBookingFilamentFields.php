<?php

declare(strict_types=1);

namespace App\Filament\Traits;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;

/**
 * HasGuestBookingFilamentFields Trait
 *
 * Provides reusable Filament table columns, filters, and form components
 * for displaying guest booking information in Admin and Owner panels.
 *
 * Usage in BookingResource:
 * ```php
 * use App\Filament\Traits\HasGuestBookingFilamentFields;
 *
 * class BookingResource extends Resource
 * {
 *     use HasGuestBookingFilamentFields;
 *
 *     public static function table(Table $table): Table
 *     {
 *         return $table
 *             ->columns([
 *                 // ... other columns
 *                 ...self::getGuestBookingColumns(),
 *             ])
 *             ->filters([
 *                 // ... other filters
 *                 ...self::getGuestBookingFilters(),
 *             ]);
 *     }
 * }
 * ```
 *
 * @package App\Filament\Traits
 * @version 1.0.0
 */
trait HasGuestBookingFilamentFields
{
    /**
     * Get table columns for guest booking display.
     *
     * Returns an array of Filament table columns that can be spread
     * into the columns array of a table definition.
     *
     * @return array<TextColumn|IconColumn>
     */
    public static function getGuestBookingColumns(): array
    {
        return [
            // Booking Type Badge Column
            TextColumn::make('booking_type_label')
                ->label(__('guest.filter_booking_type'))
                ->badge()
                ->getStateUsing(fn ($record) => $record->is_guest_booking 
                    ? ($record->hasConvertedToUser() ? __('guest.badge_guest_converted') : __('guest.badge_guest'))
                    : __('guest.badge_registered')
                )
                ->color(fn ($record) => $record->is_guest_booking
                    ? ($record->hasConvertedToUser() ? 'info' : 'warning')
                    : 'success'
                )
                ->icon(fn ($record) => $record->is_guest_booking
                    ? 'heroicon-m-user'
                    : 'heroicon-m-user-circle'
                )
                ->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query->orderBy('is_guest_booking', $direction);
                })
                ->toggleable(isToggledHiddenByDefault: false),

            // Guest Token (Hidden by default, useful for support)
            TextColumn::make('guest_token')
                ->label(__('Guest Token'))
                ->copyable()
                ->copyMessage(__('Token copied'))
                ->copyMessageDuration(1500)
                ->toggleable(isToggledHiddenByDefault: true)
                ->visible(fn ($record) => $record?->is_guest_booking ?? false)
                ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 16) . '...' : '-'),
        ];
    }

    /**
     * Get table filters for guest booking filtering.
     *
     * @return array<SelectFilter|TernaryFilter>
     */
    public static function getGuestBookingFilters(): array
    {
        return [
            // Booking Type Filter
            SelectFilter::make('booking_type')
                ->label(__('guest.filter_booking_type'))
                ->options([
                    'all' => __('All Bookings'),
                    'guest' => __('guest.filter_guest_only'),
                    'registered' => __('guest.filter_registered_only'),
                    'converted' => __('Guest (Converted to User)'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return match ($data['value'] ?? 'all') {
                        'guest' => $query->where('is_guest_booking', true)
                            ->whereNull('account_created_at'),
                        'registered' => $query->where('is_guest_booking', false),
                        'converted' => $query->where('is_guest_booking', true)
                            ->whereNotNull('account_created_at'),
                        default => $query,
                    };
                })
                ->indicator(__('guest.filter_booking_type')),

            // Simple toggle for guest bookings only
            TernaryFilter::make('is_guest_booking')
                ->label(__('Guest Booking'))
                ->placeholder(__('All'))
                ->trueLabel(__('guest.filter_guest_only'))
                ->falseLabel(__('guest.filter_registered_only'))
                ->queries(
                    true: fn (Builder $query) => $query->where('is_guest_booking', true),
                    false: fn (Builder $query) => $query->where('is_guest_booking', false),
                    blank: fn (Builder $query) => $query,
                ),
        ];
    }

    /**
     * Get form placeholders for guest booking information.
     *
     * Use in form schema to display guest booking info in view/edit forms.
     *
     * @return array<Placeholder>
     */
    public static function getGuestBookingFormFields(): array
    {
        return [
            Placeholder::make('guest_booking_info')
                ->label(__('Booking Type'))
                ->content(function ($record) {
                    if (!$record) {
                        return '-';
                    }

                    if ($record->is_guest_booking) {
                        $badge = $record->hasConvertedToUser()
                            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">' . __('guest.badge_guest_converted') . '</span>'
                            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">' . __('guest.badge_guest') . '</span>';

                        if ($record->account_created_at) {
                            $badge .= '<br><small class="text-gray-500">' . __('Account created') . ': ' . $record->account_created_at->format('M j, Y H:i') . '</small>';
                        }

                        return new \Illuminate\Support\HtmlString($badge);
                    }

                    return new \Illuminate\Support\HtmlString(
                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">' . __('guest.badge_registered') . '</span>'
                    );
                })
                ->visible(fn ($record) => $record !== null),

            Placeholder::make('guest_token_display')
                ->label(__('Guest Access Token'))
                ->content(function ($record) {
                    if (!$record?->is_guest_booking || !$record->guest_token) {
                        return '-';
                    }

                    $url = route('guest.booking.show', ['guest_token' => $record->guest_token]);
                    $tokenShort = substr($record->guest_token, 0, 16) . '...';

                    return new \Illuminate\Support\HtmlString(
                        '<div class="space-y-1">' .
                        '<code class="text-xs bg-gray-100 px-2 py-1 rounded">' . $tokenShort . '</code>' .
                        '<br><a href="' . $url . '" target="_blank" class="text-sm text-primary-600 hover:underline">' . __('View Guest Booking Page') . ' →</a>' .
                        '</div>'
                    );
                })
                ->visible(fn ($record) => $record?->is_guest_booking ?? false),
        ];
    }

    /**
     * Get infolist entries for guest booking display.
     *
     * Use in infolist schema for view pages.
     *
     * @return array<TextEntry|IconEntry>
     */
    public static function getGuestBookingInfolistEntries(): array
    {
        return [
            TextEntry::make('booking_type_label')
                ->label(__('guest.filter_booking_type'))
                ->badge()
                ->getStateUsing(fn ($record) => $record->is_guest_booking
                    ? ($record->hasConvertedToUser() ? __('guest.badge_guest_converted') : __('guest.badge_guest'))
                    : __('guest.badge_registered')
                )
                ->color(fn ($record) => $record->is_guest_booking
                    ? ($record->hasConvertedToUser() ? 'info' : 'warning')
                    : 'success'
                ),

            TextEntry::make('guest_token')
                ->label(__('Guest Access Token'))
                ->copyable()
                ->copyMessage(__('Token copied'))
                ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 16) . '...' : '-')
                ->visible(fn ($record) => $record->is_guest_booking),

            TextEntry::make('guest_booking_url')
                ->label(__('Guest Booking URL'))
                ->getStateUsing(fn ($record) => $record->guest_booking_url)
                ->url(fn ($record) => $record->guest_booking_url)
                ->openUrlInNewTab()
                ->visible(fn ($record) => $record->is_guest_booking && $record->guest_token),

            TextEntry::make('account_created_at')
                ->label(__('Account Created'))
                ->dateTime()
                ->visible(fn ($record) => $record->is_guest_booking && $record->account_created_at),
        ];
    }

    /**
     * Get the customer name with guest indicator.
     *
     * Helper method to format customer name display with guest badge.
     *
     * @param mixed $record The booking record
     * @return string
     */
    public static function formatCustomerNameWithGuestBadge($record): string
    {
        $name = $record->customer_name ?? $record->user?->name ?? __('Unknown');

        if ($record->is_guest_booking) {
            return $name . ' (' . __('Guest') . ')';
        }

        return $name;
    }

    /**
     * Modify the base query to include guest booking eager loading.
     *
     * @param Builder $query
     * @return Builder
     */
    public static function withGuestBookingRelations(Builder $query): Builder
    {
        return $query->with(['guestSession']);
    }
}
