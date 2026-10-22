<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;

/**
 * Full hall details screen. Contact fields are the same ones the public
 * website shows on the hall page.
 */
class HallResource extends HallListResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $terms = $this->terms_and_conditions;

        return array_merge(parent::toArray($request), [
            'description' => $this->description,
            'address' => $this->address,
            'address_localized' => $this->address_localized,
            'google_maps_url' => $this->google_maps_url,
            'video_url' => $this->video_url,
            'virtual_tour_url' => $this->virtual_tour_url,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'instagram' => $this->instagram,
            'terms_and_conditions' => is_array($terms) ? ($terms[$locale] ?? $terms['en'] ?? null) : $terms,
            'faq' => $this->faq ?? [],
            'is_24_hours' => (bool) $this->is_24_hours,
            'function_hours' => $this->function_hours ?? [],
            'special_hours_note' => $this->special_hours_note,
            'slot_prices' => collect(['morning', 'afternoon', 'evening', 'full_day'])
                ->mapWithKeys(fn (string $slot) => [$slot => static::money($this->getPriceForSlot($slot))]),
            'cancellation_hours' => $this->cancellation_hours,
            'cancellation_fee_percentage' => $this->cancellation_fee_percentage !== null
                ? (float) $this->cancellation_fee_percentage
                : null,
            'advance_payment' => [
                'enabled' => (bool) $this->allows_advance_payment,
                'type' => $this->advance_payment_type,
                'amount' => static::money($this->advance_payment_amount),
                'percentage' => $this->advance_payment_percentage !== null ? (float) $this->advance_payment_percentage : null,
                'minimum' => static::money($this->minimum_advance_payment),
            ],
            'images' => $this->whenLoaded('activeImages', fn () => $this->activeImages->map(fn ($image) => [
                'id' => $image->id,
                'url' => static::fileUrl($image->image_path),
                'thumbnail_url' => static::fileUrl($image->thumbnail_path),
                'caption' => $image->caption,
            ])),
            'gallery_urls' => collect($this->gallery ?? [])->map(fn ($path) => static::fileUrl($path))->filter()->values(),
            'features' => LookupResource::collection($this->whenLoaded('featureList')),
            'extra_services' => ExtraServiceResource::collection($this->whenLoaded('activeExtraServices')),
            'reviews' => ReviewResource::collection($this->whenLoaded('approvedReviews')),
        ]);
    }
}
