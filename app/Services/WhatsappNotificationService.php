<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use WallaceMartinss\FilamentEvolution\Enums\StatusConnectionEnum;
use WallaceMartinss\FilamentEvolution\Models\WhatsappInstance;
use WallaceMartinss\FilamentEvolution\Services\EvolutionClient;

class WhatsappNotificationService
{
    private const DIVIDER = "──────────────────────────";

    public function __construct(private readonly EvolutionClient $client) {}

    public function sendBookingCreatedToCustomer(Booking $booking): void
    {
        $phone = $this->formatPhone($booking->customer_phone);
        if (!$phone) {
            Log::warning('WhatsApp: skipping customer notification — no phone', ['booking_id' => $booking->id]);
            return;
        }

        $instance = $this->getActiveInstance();
        if (!$instance) {
            Log::warning('WhatsApp: no active instance, skipping customer notification', ['booking_id' => $booking->id]);
            return;
        }

        $booking->loadMissing(['hall']);

        $hasLink     = !empty($booking->guest_token);
        $templateKey = $hasLink ? 'customer_booking_created' : 'customer_booking_created_no_link';

        $sharedParams = [
            'booking_number' => $booking->booking_number,
            'date'           => $booking->booking_date->format('d M Y'),
            'guests'         => $booking->number_of_guests,
            'total'          => number_format((float) $booking->total_amount, 3),
        ];

        if ($hasLink) {
            $sharedParams['booking_link'] = route('guest.booking.show', $booking->guest_token);
        }

        $enMessage = trans('booking.whatsapp.' . $templateKey, array_merge($sharedParams, [
            'hall_name' => $this->getHallName($booking, 'en'),
            'time_slot' => $this->translateTimeSlot($booking->time_slot, 'en'),
        ]), 'en');

        $arMessage = trans('booking.whatsapp.' . $templateKey, array_merge($sharedParams, [
            'hall_name' => $this->getHallName($booking, 'ar'),
            'time_slot' => $this->translateTimeSlot($booking->time_slot, 'ar'),
        ]), 'ar');

        $this->send(
            $instance,
            $phone,
            $enMessage . "\n\n" . self::DIVIDER . "\n\n" . $arMessage,
            $booking->id,
            'customer_booking_created'
        );
    }

    public function sendBookingCreatedToOwner(Booking $booking): void
    {
        $booking->loadMissing(['hall', 'hall.owner']);

        $ownerPhone = $booking->hall->owner->phone ?? null;
        $phone      = $this->formatPhone($ownerPhone);

        if (!$phone) {
            Log::warning('WhatsApp: skipping owner notification — no phone', ['booking_id' => $booking->id]);
            return;
        }

        $instance = $this->getActiveInstance();
        if (!$instance) {
            Log::warning('WhatsApp: no active instance, skipping owner notification', ['booking_id' => $booking->id]);
            return;
        }

        $sharedParams = [
            'booking_number' => $booking->booking_number,
            'customer_name'  => $booking->customer_name,
            'customer_phone' => $booking->customer_phone ?? '-',
            'date'           => $booking->booking_date->format('d M Y'),
            'guests'         => $booking->number_of_guests,
            'payout'         => number_format((float) $booking->owner_payout, 3),
        ];

        $enMessage = trans('booking.whatsapp.owner_booking_created', array_merge($sharedParams, [
            'hall_name' => $this->getHallName($booking, 'en'),
            'time_slot' => $this->translateTimeSlot($booking->time_slot, 'en'),
        ]), 'en');

        $arMessage = trans('booking.whatsapp.owner_booking_created', array_merge($sharedParams, [
            'hall_name' => $this->getHallName($booking, 'ar'),
            'time_slot' => $this->translateTimeSlot($booking->time_slot, 'ar'),
        ]), 'ar');

        $this->send(
            $instance,
            $phone,
            $enMessage . "\n\n" . self::DIVIDER . "\n\n" . $arMessage,
            $booking->id,
            'owner_booking_created'
        );
    }

    private function send(WhatsappInstance $instance, string $phone, string $message, int $bookingId, string $type): void
    {
        try {
            $this->client->sendText($instance->name, $phone, $message);

            Log::info('WhatsApp notification sent', [
                'booking_id' => $bookingId,
                'type'       => $type,
                'phone'      => $phone,
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp notification failed', [
                'booking_id' => $bookingId,
                'type'       => $type,
                'phone'      => $phone,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    private function getActiveInstance(): ?WhatsappInstance
    {
        return WhatsappInstance::where('status', StatusConnectionEnum::OPEN)->first();
    }

    private function formatPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Strip everything except digits
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($phone)) {
            return null;
        }

        // Already has Oman country code
        if (str_starts_with($phone, '968')) {
            return $phone;
        }

        // Leading zero (local format) — strip it and prepend Oman code
        if (str_starts_with($phone, '0')) {
            return '968' . substr($phone, 1);
        }

        return '968' . $phone;
    }

    private function getHallName(Booking $booking, string $locale): string
    {
        $name = $booking->hall->name ?? null;

        if (!$name) {
            return $locale === 'ar' ? 'القاعة' : 'Hall';
        }

        if (is_array($name)) {
            return $name[$locale] ?? $name['en'] ?? ($locale === 'ar' ? 'القاعة' : 'Hall');
        }

        return (string) $name;
    }

    private function translateTimeSlot(string $slot, string $locale): string
    {
        $labels = [
            'en' => [
                'morning'   => 'Morning',
                'afternoon' => 'Afternoon',
                'evening'   => 'Evening',
                'full_day'  => 'Full Day',
            ],
            'ar' => [
                'morning'   => 'صباحي',
                'afternoon' => 'بعد الظهر',
                'evening'   => 'مسائي',
                'full_day'  => 'يوم كامل',
            ],
        ];

        return $labels[$locale][$slot] ?? $slot;
    }
}
