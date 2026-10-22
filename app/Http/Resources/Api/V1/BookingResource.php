<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * A customer's booking. Owner-side figures (commission, owner payout) are never included.
 *
 * @property Booking $resource
 */
class BookingResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        /** @var Booking $booking */
        $booking = $this->resource;

        $days = $booking->relationLoaded('bookingDays') && $booking->bookingDays->isNotEmpty()
            ? $booking->bookingDays->map(fn ($day) => [
                'date' => Carbon::parse($day->date)->toDateString(),
                'time_slot' => $day->time_slot,
                'price' => static::money($day->price),
            ])->values()
            : collect([[
                'date' => $booking->booking_date?->toDateString(),
                'time_slot' => $booking->time_slot,
                'price' => static::money($booking->hall_price),
            ]]);

        $isAdvance = $booking->payment_type === 'advance';

        return [
            'booking_number' => $booking->booking_number,
            'status' => $booking->status,
            'payment_status' => $booking->payment_status,
            'hall' => $this->whenLoaded('hall', fn () => [
                'id' => $booking->hall->id,
                'slug' => $booking->hall->slug,
                'name' => $booking->hall->name,
                'featured_image_url' => static::fileUrl($booking->hall->featured_image),
                'city' => $booking->hall->relationLoaded('city') ? $booking->hall->city?->name : null,
                'phone' => $booking->hall->phone,
                'latitude' => $booking->hall->latitude !== null ? (float) $booking->hall->latitude : null,
                'longitude' => $booking->hall->longitude !== null ? (float) $booking->hall->longitude : null,
            ]),
            'days' => $days,
            'number_of_guests' => $booking->number_of_guests,
            'event_types' => LookupResource::collection($this->whenLoaded('eventTypes')),
            'customer_name' => $booking->customer_name,
            'customer_email' => $booking->customer_email,
            'customer_phone' => $booking->customer_phone,
            'customer_notes' => $booking->customer_notes,
            'extra_services' => $this->whenLoaded('extraServices', fn () => $booking->extraServices->map(fn ($service) => [
                'name' => $service->localized_name,
                'quantity' => $service->quantity,
                'price' => static::money($service->total_price),
            ])->values()),
            'pricing' => [
                'currency' => config('majalis.currency'),
                'hall_price' => static::money($booking->hall_price),
                'services_price' => static::money($booking->services_price),
                'subtotal' => static::money($booking->subtotal),
                'service_fee' => static::money($booking->platform_fee),
                'discount' => static::money($booking->discount_amount),
                'total' => static::money($booking->total_amount),
            ],
            'payment' => [
                'type' => $booking->payment_type ?? 'full',
                'advance_amount' => $isAdvance ? static::money($booking->advance_amount) : null,
                // Paid to the hall directly; the owner marks it received.
                'balance_due' => $isAdvance ? static::money($booking->balance_due) : null,
                'balance_paid' => $isAdvance ? $booking->balance_paid_at !== null : null,
                'amount_due_now' => static::money(static::amountDueNow($booking)),
            ],
            'can_pay' => static::canPay($booking),
            'can_cancel' => static::canCancel($booking),
            'can_review' => $booking->status === 'completed' && $booking->canReceiveReview(),
            'refund_amount' => static::money($booking->refund_amount),
            'cancellation_reason' => $booking->cancellation_reason,
            'created_at' => $booking->created_at?->toIso8601String(),
            'confirmed_at' => $booking->confirmed_at?->toIso8601String(),
            'cancelled_at' => $booking->cancelled_at?->toIso8601String(),
        ];
    }

    /** Online payment is open while the booking is unpaid, not cancelled and still ahead. */
    public static function canPay(Booking $booking): bool
    {
        return $booking->status === 'pending'
            && in_array($booking->payment_status, ['pending', 'failed'], true)
            && $booking->booking_date !== null
            && $booking->booking_date->gte(today())
            && static::amountDueNow($booking) > 0;
    }

    public static function amountDueNow(Booking $booking): float
    {
        if (! in_array($booking->payment_status, ['pending', 'failed'], true)) {
            return 0.0;
        }

        return $booking->payment_type === 'advance' && $booking->advance_amount
            ? (float) $booking->advance_amount
            : (float) $booking->total_amount;
    }

    /** Pending or confirmed bookings can be cancelled until the first day starts. */
    public static function canCancel(Booking $booking): bool
    {
        return in_array($booking->status, ['pending', 'confirmed'], true)
            && $booking->booking_date !== null
            && $booking->booking_date->gte(today());
    }
}
