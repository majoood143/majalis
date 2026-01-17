<?php

declare(strict_types=1);

namespace App\Filament\Components;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Guest Booking Table Components
 *
 * Provides reusable Filament table columns and filters for displaying
 * and filtering guest booking information in Admin/Owner panels.
 *
 * Usage in BookingResource::table():
 *
 * use App\Filament\Components\GuestBookingComponents;
 *
 * public static function table(Table $table): Table
 * {
 *     return $table
 *         ->columns([
 *             // ... other columns
 *             GuestBookingComponents::guestBadgeColumn(),
 *             GuestBookingComponents::bookingTypeColumn(),
 *         ])
 *         ->filters([
 *             GuestBookingComponents::bookingTypeFilter(),
 *         ]);
 * }
 *
 * @package App\Filament\Components
 * @version 1.0.0
 */
class GuestBookingComponents
{
    /**
     * Create a badge column indicating if booking is guest or registered.
     *
     * Displays:
     * - "Guest" badge (yellow) for guest bookings
     * - "Guest (Converted)" badge (blue) for converted guests
     * - "Registered" badge (green) for registered user bookings
     *
     * @return TextColumn
     */
    public static function guestBadgeColumn(): TextColumn
    {
        return TextColumn::make('booking_type_label')
            ->label(__('guest.filter_booking_type'))
            ->badge()
            ->getStateUsing(function ($record): string {
                if (!$record->is_guest_booking) {
                    return __('guest.badge_registered');
                }

                if ($record->account_created_at !== null) {
                    return __('guest.badge_guest_converted');
                }

                return __('guest.badge_guest');
            })
            ->color(fn (string $state): string => match ($state) {
                __('guest.badge_guest') => 'warning',
                __('guest.badge_guest_converted') => 'info',
                __('guest.badge_registered') => 'success',
                default => 'gray',
            })
            ->sortable(query: function (Builder $query, string $direction): Builder {
                return $query->orderBy('is_guest_booking', $direction);
            });
    }

    /**
     * Create an icon column showing guest status.
     *
     * Shows a user icon with different colors:
     * - Gray user-minus icon for guests
     * - Blue user-check icon for converted guests
     * - Green user icon for registered users
     *
     * @return IconColumn
     */
    public static function guestIconColumn(): IconColumn
    {
        return IconColumn::make('is_guest_booking')
            ->label(__('Type'))
            ->boolean()
            ->trueIcon('heroicon-o-user-minus')
            ->falseIcon('heroicon-o-user')
            ->trueColor('warning')
            ->falseColor('success')
            ->tooltip(fn ($record): string => $record->is_guest_booking
                ? __('guest.badge_guest')
                : __('guest.badge_registered')
            );
    }

    /**
     * Create a text column for booking type with detailed info.
     *
     * Shows the booking type with customer email for guests.
     *
     * @return TextColumn
     */
    public static function bookingTypeColumn(): TextColumn
    {
        return TextColumn::make('is_guest_booking')
            ->label(__('Booking Type'))
            ->formatStateUsing(function ($state, $record): string {
                if (!$state) {
                    return __('guest.badge_registered');
                }

                $label = __('guest.badge_guest');
                if ($record->account_created_at !== null) {
                    $label = __('guest.badge_guest_converted');
                }

                return $label;
            })
            ->description(fn ($record): ?string => $record->is_guest_booking
                ? $record->customer_email
                : null
            )
            ->icon(fn ($state): string => $state
                ? 'heroicon-m-user-minus'
                : 'heroicon-m-user'
            )
            ->iconColor(fn ($state, $record): string => match (true) {
                !$state => 'success',
                $record->account_created_at !== null => 'info',
                default => 'warning',
            });
    }

    /**
     * Create a select filter for booking type.
     *
     * Options:
     * - All bookings (default)
     * - Guest bookings only
     * - Registered users only
     * - Converted guests only
     *
     * @return SelectFilter
     */
    public static function bookingTypeFilter(): SelectFilter
    {
        return SelectFilter::make('booking_type')
            ->label(__('guest.filter_booking_type'))
            ->options([
                'guest' => __('guest.filter_guest_only'),
                'registered' => __('guest.filter_registered_only'),
                'converted' => __('Guest (Converted)'),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return match ($data['value'] ?? null) {
                    'guest' => $query->where('is_guest_booking', true)
                        ->whereNull('account_created_at'),
                    'registered' => $query->where('is_guest_booking', false),
                    'converted' => $query->where('is_guest_booking', true)
                        ->whereNotNull('account_created_at'),
                    default => $query,
                };
            })
            ->indicator(__('Booking Type'));
    }

    /**
     * Create a simple toggle filter for guest bookings.
     *
     * @return Filter
     */
    public static function guestOnlyFilter(): Filter
    {
        return Filter::make('guest_only')
            ->label(__('guest.filter_guest_only'))
            ->query(fn (Builder $query): Builder => $query->where('is_guest_booking', true))
            ->toggle();
    }

    /**
     * Create a column showing guest token (for admin reference).
     *
     * Only visible for guest bookings. Shows truncated token with copy button.
     *
     * @return TextColumn
     */
    public static function guestTokenColumn(): TextColumn
    {
        return TextColumn::make('guest_token')
            ->label(__('Guest Token'))
            ->limit(12)
            ->tooltip(fn ($record): ?string => $record->guest_token)
            ->copyable()
            ->copyMessage(__('Token copied'))
            ->visible(fn ($record): bool => $record?->is_guest_booking ?? false)
            ->toggleable(isToggledHiddenByDefault: true);
    }

    /**
     * Create a column showing guest access URL.
     *
     * Displays a clickable link to the guest booking page.
     *
     * @return TextColumn
     */
    public static function guestAccessLinkColumn(): TextColumn
    {
        return TextColumn::make('guest_booking_url')
            ->label(__('Guest Access Link'))
            ->url(fn ($record): ?string => $record->guest_booking_url)
            ->openUrlInNewTab()
            ->visible(fn ($record): bool => $record?->is_guest_booking ?? false)
            ->toggleable(isToggledHiddenByDefault: true);
    }

    /**
     * Create a column showing when guest converted to user.
     *
     * @return TextColumn
     */
    public static function accountCreatedColumn(): TextColumn
    {
        return TextColumn::make('account_created_at')
            ->label(__('Account Created'))
            ->dateTime()
            ->sortable()
            ->visible(fn ($record): bool => $record?->is_guest_booking && $record?->account_created_at !== null)
            ->toggleable(isToggledHiddenByDefault: true);
    }

    /**
     * Get all guest-related columns as an array.
     *
     * @return array
     */
    public static function allColumns(): array
    {
        return [
            self::guestBadgeColumn(),
            self::guestTokenColumn(),
            self::accountCreatedColumn(),
        ];
    }

    /**
     * Get all guest-related filters as an array.
     *
     * @return array
     */
    public static function allFilters(): array
    {
        return [
            self::bookingTypeFilter(),
        ];
    }
}
