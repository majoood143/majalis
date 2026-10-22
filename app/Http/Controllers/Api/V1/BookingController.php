<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BookingResource;
use App\Models\Booking;
use App\Models\ExtraService;
use App\Models\Hall;
use App\Models\Payment;
use App\Models\PromoCode;
use App\Services\BookingPdfService;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Services\PromoCodeService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Customer bookings for the mobile app: quote → create → pay (Thawani) → manage.
 *
 * Pricing and availability come only from BookingService (quote() and
 * createBooking() share priceBreakdown()), so the app never computes a price.
 */
class BookingController extends Controller
{
    protected const RELATIONS = ['hall.city', 'bookingDays', 'extraServices', 'eventTypes'];

    public function __construct(protected BookingService $bookingService) {}

    /**
     * Price and availability for a selection, before booking. Writes nothing.
     */
    public function quote(Request $request): JsonResponse
    {
        [$hall, $days, $validated] = $this->validateSelection($request);

        $quote = $this->bookingService->quote(
            $hall,
            $days,
            $this->serviceIds($hall, $validated['extra_services'] ?? []),
            $validated['promo_code'] ?? null,
            $validated['payment_type'] ?? 'full',
        );

        return response()->json(['data' => $this->presentQuote($hall, $days, $quote)]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        [$hall, $days, $validated] = $this->validateSelection($request, [
            'number_of_guests' => ['required', 'integer', 'min:1'],
            'event_type_id' => ['nullable', 'integer', Rule::exists('event_types', 'id')->where('is_active', true)],
            'customer_name' => ['nullable', 'string', 'max:255'],
            // Falls back to the account's phone; required only when the account has none.
            'customer_phone' => [$user->phone ? 'nullable' : 'required', 'string', 'regex:/^[0-9+\s-]{7,20}$/'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->validate([
            'number_of_guests' => ['integer', "between:{$hall->capacity_min},{$hall->capacity_max}"],
        ]);

        $guests = (int) $validated['number_of_guests'];
        $phone = $validated['customer_phone'] ?? $user->phone;

        $serviceIds = $this->serviceIds($hall, $validated['extra_services'] ?? []);
        $paymentType = $validated['payment_type'] ?? 'full';
        $quote = $this->bookingService->quote($hall, $days, $serviceIds, $validated['promo_code'] ?? null, $paymentType);

        if ($quote['unavailable_days'] !== []) {
            return $this->unavailableResponse($quote['unavailable_days']);
        }
        if ($quote['promo'] && ! $quote['promo']['valid']) {
            throw ValidationException::withMessages(['promo_code' => [$quote['promo']['message']]]);
        }

        try {
            $booking = DB::transaction(function () use ($hall, $days, $validated, $user, $guests, $phone, $serviceIds, $quote, $paymentType) {
                $booking = $this->bookingService->createBooking([
                    'hall_id' => $hall->id,
                    'user_id' => $user->id,
                    'days' => $days,
                    'number_of_guests' => $guests,
                    'customer_name' => $validated['customer_name'] ?? $user->name,
                    'customer_email' => $user->email,
                    'customer_phone' => $phone,
                    'customer_notes' => $validated['customer_notes'] ?? null,
                    'extra_services' => $serviceIds,
                ]);

                if (isset($validated['event_type_id'])) {
                    $booking->eventTypes()->sync([$validated['event_type_id']]);
                }

                if ($quote['promo']['promo_code_id'] ?? null) {
                    app(PromoCodeService::class)->applyToBooking(PromoCode::findOrFail($quote['promo']['promo_code_id']), $booking);
                    $booking->refresh();
                }

                return $this->bookingService->applyPaymentPlan($booking, $paymentType);
            });
        } catch (Exception $e) {
            // Most likely another booking took one of the slots a moment ago (unique slot key).
            Log::warning('API booking creation failed', ['hall_id' => $hall->id, 'user_id' => $user->id, 'error' => $e->getMessage()]);

            $unavailable = array_values(array_filter(
                $days,
                fn (array $day) => ! $this->bookingService->isDayBookable($hall, $day['date'], $day['time_slot']),
            ));

            return $unavailable !== []
                ? $this->unavailableResponse($unavailable)
                : response()->json(['message' => __('halls.booking_failed')], 500);
        }

        return (new BookingResource($booking->load(self::RELATIONS)))
            ->response()
            ->setStatusCode(201);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['upcoming', 'past', 'cancelled'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $today = today()->toDateString();

        $bookings = $request->user()->bookings()
            ->with(self::RELATIONS)
            ->when($validated['status'] ?? null, fn (Builder $query, string $status) => match ($status) {
                'upcoming' => $query->whereIn('status', ['pending', 'confirmed'])
                    ->where('booking_date', '>=', $today)
                    ->orderBy('booking_date'),
                'past' => $query->where(fn (Builder $q) => $q->where('status', 'completed')
                    ->orWhere(fn (Builder $q) => $q->whereIn('status', ['pending', 'confirmed'])->where('booking_date', '<', $today)))
                    ->orderByDesc('booking_date'),
                'cancelled' => $query->whereIn('status', ['cancelled', 'refunded'])->orderByDesc('cancelled_at'),
            }, fn (Builder $query) => $query->latest())
            ->paginate((int) ($validated['per_page'] ?? 15))
            ->withQueryString();

        return BookingResource::collection($bookings);
    }

    public function show(Request $request, string $bookingNumber): BookingResource
    {
        return new BookingResource($this->findOwn($request, $bookingNumber)->load(self::RELATIONS));
    }

    /**
     * Start (or retry) the online payment. Returns Thawani's hosted checkout URL;
     * the app opens it and gets control back at majalis://payment/result.
     */
    public function pay(Request $request, string $bookingNumber, PaymentService $paymentService): JsonResponse
    {
        $booking = $this->findOwn($request, $bookingNumber)->load('hall');

        if (! BookingResource::canPay($booking)) {
            return response()->json(['message' => __('halls.payment_not_allowed')], 409);
        }

        $result = $paymentService->initiatePayment($booking, 'online', fn (Payment $payment) => [
            'success' => route('api.v1.payments.return', ['reference' => $payment->payment_reference, 'result' => 'success']),
            'cancel' => route('api.v1.payments.return', ['reference' => $payment->payment_reference, 'result' => 'cancel']),
        ]);

        if (! ($result['success'] ?? false) || empty($result['redirect_url'])) {
            return response()->json(['message' => $result['message'] ?? __('halls.payment_failed')], 502);
        }

        return response()->json(['data' => [
            'payment_url' => $result['redirect_url'],
            'amount' => BookingResource::amountDueNow($booking),
            'currency' => config('majalis.currency'),
            'return_url_prefix' => config('majalis.app_scheme', 'majalis').'://payment/result',
        ]]);
    }

    /**
     * Same rules as Booking::cancel(): a paid booking gets the hall's cancellation fee
     * deducted from the refund, which an admin then processes.
     */
    public function cancel(Request $request, string $bookingNumber): BookingResource|JsonResponse
    {
        $validated = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);
        $booking = $this->findOwn($request, $bookingNumber);

        if (! BookingResource::canCancel($booking)) {
            return response()->json(['message' => __('halls.cannot_cancel')], 409);
        }

        $booking->cancel($validated['reason'] ?? 'Cancelled by customer (app)');

        return new BookingResource($booking->refresh()->load(self::RELATIONS));
    }

    public function invoice(Request $request, string $bookingNumber, BookingPdfService $pdfService): Response
    {
        return $pdfService->download($this->findOwn($request, $bookingNumber));
    }

    /**
     * @param  array<string, mixed>  $extraRules
     * @return array{0: Hall, 1: array<int, array{date: string, time_slot: string}>, 2: array<string, mixed>}
     */
    protected function validateSelection(Request $request, array $extraRules = []): array
    {
        $validated = $request->validate(array_merge([
            'hall_id' => ['required', 'integer', Rule::exists('halls', 'id')->where('is_active', true)->whereNull('deleted_at')],
            'days' => ['required', 'array', 'min:1', 'max:3'],
            'days.*.date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'days.*.time_slot' => ['required', Rule::in(['morning', 'afternoon', 'evening', 'full_day'])],
            'extra_services' => ['nullable', 'array'],
            'extra_services.*' => ['integer'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'payment_type' => ['nullable', Rule::in(['full', 'advance'])],
        ], $extraRules));

        $days = collect($validated['days'])
            ->map(fn (array $day) => ['date' => $day['date'], 'time_slot' => $day['time_slot']])
            ->sortBy('date')
            ->values();

        if ($days->unique('date')->count() !== $days->count()) {
            throw ValidationException::withMessages(['days' => [__('halls.duplicate_day_selection')]]);
        }

        return [Hall::findOrFail($validated['hall_id']), $days->all(), $validated];
    }

    /**
     * The chosen services plus the hall's required ones (active, this hall only).
     *
     * @param  array<int, int>  $requested
     * @return array<int, int>
     */
    protected function serviceIds(Hall $hall, array $requested): array
    {
        $required = ExtraService::where('hall_id', $hall->id)
            ->where('is_active', true)
            ->where('is_required', true)
            ->pluck('id')
            ->all();

        return array_values(array_unique(array_map('intval', array_merge($requested, $required))));
    }

    /**
     * @param  array<int, array{date: string, time_slot: string}>  $days
     * @param  array<string, mixed>  $quote
     */
    protected function presentQuote(Hall $hall, array $days, array $quote): array
    {
        $pricing = $quote['pricing'];
        $unavailable = collect($quote['unavailable_days'])->map(fn (array $day) => $day['date'].'|'.$day['time_slot']);

        return [
            'hall_id' => $hall->id,
            'currency' => config('majalis.currency'),
            'available' => $quote['unavailable_days'] === [],
            'days' => collect($days)->values()->map(fn (array $day, int $i) => [
                'date' => $day['date'],
                'time_slot' => $day['time_slot'],
                'price' => round((float) $pricing['day_prices'][$i], 3),
                'available' => ! $unavailable->contains($day['date'].'|'.$day['time_slot']),
            ]),
            'extra_services' => collect($pricing['services'])->map(fn (array $service) => [
                'id' => $service['id'],
                'name' => $service['name'][app()->getLocale()] ?? $service['name']['en'] ?? reset($service['name']),
                'price' => round($service['price'], 3),
            ])->values(),
            'hall_price' => round($pricing['hall_price'], 3),
            'services_price' => round($pricing['services_price'], 3),
            'subtotal' => round((float) $pricing['subtotal'], 3),
            'service_fee' => round($pricing['platform_fee'], 3),
            'discount' => $quote['discount_amount'],
            'total' => $quote['total_amount'],
            'promo' => $quote['promo'] ? [
                'code' => $quote['promo']['code'],
                'valid' => $quote['promo']['valid'],
                'message' => $quote['promo']['message'],
            ] : null,
            'payment' => array_merge($quote['payment'], [
                'advance_available' => $hall->requiresAdvancePayment(),
            ]),
            // Both choices, so the app can show "pay 30% now" next to "pay in full".
            'payment_options' => collect(['advance', 'full'])
                ->map(fn (string $type) => $this->bookingService->paymentPlan(
                    $hall,
                    (float) $pricing['subtotal'] - $quote['discount_amount'],
                    (float) $pricing['platform_fee'],
                    $quote['total_amount'],
                    $type,
                ))
                ->unique('type')
                ->values(),
        ];
    }

    /**
     * @param  array<int, array{date: string, time_slot: string}>  $days
     */
    protected function unavailableResponse(array $days): JsonResponse
    {
        return response()->json([
            'message' => __('halls.date_not_available'),
            'unavailable_days' => array_values($days),
        ], 409);
    }

    protected function findOwn(Request $request, string $bookingNumber): Booking
    {
        return $request->user()->bookings()->where('booking_number', $bookingNumber)->firstOrFail();
    }
}
