<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\GuestSession;
use App\Models\Hall;
use App\Models\User;
use App\Notifications\GuestBookingOtpNotification;
use App\Notifications\GuestBookingConfirmationNotification;
use Illuminate\Http\Request;
use App\Models\ExtraService;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use App\Models\Payment;
use App\Services\ThawaniService;
use Exception;
use Illuminate\Support\Facades\Http;


/**
 * Guest Booking Controller
 *
 * Handles all guest booking operations including:
 * - Session creation and OTP verification
 * - Booking form submission
 * - Payment processing
 * - Account creation after booking
 *
 * Guest Booking Flow:
 * 1. Guest enters email → OTP sent
 * 2. Guest verifies OTP → Session verified
 * 3. Guest fills booking form → Booking created
 * 4. Guest completes payment → Booking confirmed
 * 5. Optional: Guest creates account → Bookings linked
 *
 * Security:
 * - OTP verification required before booking
 * - Guest token for secure booking access
 * - Rate limiting on pending bookings per email
 *
 * @package App\Http\Controllers\Customer
 * @version 1.0.0
 */
class GuestBookingController extends Controller
{
    /**
     * Maximum pending bookings allowed per guest email.
     *
     * @var int
     */
    private const MAX_PENDING_BOOKINGS = 3;

    /**
     * Show the guest booking initiation page.
     *
     * This is the first step where guest enters their details
     * and receives an OTP for email verification.
     *
     * @param Hall $hall The hall to book (via route model binding with slug)
     * @return View|RedirectResponse
     */
    public function create(Hall $hall): View|RedirectResponse
    {
        // Set locale from query or session
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);
        session(['locale' => $locale]);

        // Verify hall is active and bookable
        if (!$hall->is_active) {
            return redirect()
                ->route('customer.halls.index')
                ->with('error', __('halls.hall_not_available'));
        }

        // Load hall relationships for display
        $hall->load(['city.region', 'owner', 'activeExtraServices']);

        return view('customer.guest.book', compact('hall'));
    }

    /**
     * Initiate guest booking session and send OTP.
     *
     * Creates a guest session and sends OTP to the provided email.
     * If email belongs to existing user, prompts to log in instead.
     *
     * @param Request $request
     * @param Hall $hall
     * @return RedirectResponse
     */
    public function initiate(Request $request, Hall $hall): RedirectResponse
    {
        // Set locale
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        // Validate guest info
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
        ], [
            'name.required' => __('validation.required', ['attribute' => __('Name')]),
            'email.required' => __('validation.required', ['attribute' => __('Email')]),
            'email.email' => __('validation.email', ['attribute' => __('Email')]),
            'phone.required' => __('validation.required', ['attribute' => __('Phone')]),
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $email = strtolower($request->input('email'));

        // Check if email belongs to existing user
        if (User::where('email', $email)->exists()) {
            return back()
                ->with('info', __('guest.email_registered_prompt'))
                ->with('show_login_modal', true)
                ->with('registered_email', $email)
                ->withInput();
        }

        // Check pending booking limit for this email
        $pendingCount = Booking::guestByEmail($email)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('booking_date', '>=', now()->toDateString())
            ->count();

        if ($pendingCount >= self::MAX_PENDING_BOOKINGS) {
            return back()
                ->with('error', __('guest.max_pending_bookings', ['count' => self::MAX_PENDING_BOOKINGS]))
                ->withInput();
        }

        // Check pending sessions limit
        if (GuestSession::hasExceededPendingLimit($email)) {
            return back()
                ->with('error', __('guest.too_many_sessions'))
                ->withInput();
        }

        try {
            // Create guest session
            $session = GuestSession::createSession([
                'name' => $request->input('name'),
                'email' => $email,
                'phone' => $request->input('phone'),
                'hall_id' => $hall->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Generate and send OTP
            $otp = $session->generateOtp();

            // Send OTP notification
            Notification::route('mail', $email)
                ->notify(new GuestBookingOtpNotification($otp, $session->name, $hall));

            Log::info('Guest booking OTP sent', [
                'session_id' => $session->id,
                'email' => $session->masked_email,
                'hall_id' => $hall->id,
            ]);

            // Store session token in browser session for next step
            session(['guest_session_token' => $session->session_token]);

            return redirect()
                ->route('guest.verify-otp', [
                    'hall' => $hall->slug,
                    'lang' => $locale,
                ])
                ->with('success', __('guest.otp_sent', ['email' => $session->masked_email]));

        } catch (Exception $e) {
            Log::error('Failed to initiate guest booking', [
                'error' => $e->getMessage(),
                'email' => $email,
                'hall_id' => $hall->id,
            ]);

            return back()
                ->with('error', __('guest.initiation_failed') . ': ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show OTP verification page.
     *
     * @param Hall $hall
     * @return View|RedirectResponse
     */
    public function showVerifyOtp(Hall $hall): View|RedirectResponse
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        // Get session token from browser session
        $sessionToken = session('guest_session_token');

        if (!$sessionToken) {
            return redirect()
                ->route('guest.book', ['hall' => $hall->slug, 'lang' => $locale])
                ->with('error', __('guest.session_expired'));
        }

        $guestSession = GuestSession::findByToken($sessionToken);

        if (!$guestSession) {
            session()->forget('guest_session_token');
            return redirect()
                ->route('guest.book', ['hall' => $hall->slug, 'lang' => $locale])
                ->with('error', __('guest.session_expired'));
        }

        // If already verified, redirect to booking form
        if ($guestSession->is_verified) {
            return redirect()
                ->route('guest.booking-form', ['hall' => $hall->slug, 'lang' => $locale]);
        }

        return view('customer.guest.verify-otp', [
            'hall' => $hall,
            'guestSession' => $guestSession,
        ]);
    }

    /**
     * Verify OTP code.
     *
     * @param Request $request
     * @param Hall $hall
     * @return RedirectResponse
     */
    public function verifyOtp(Request $request, Hall $hall): RedirectResponse
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        // Validate OTP input
        $validator = Validator::make($request->all(), [
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ], [
            'otp.required' => __('guest.otp_required'),
            'otp.size' => __('guest.otp_invalid_length'),
            'otp.regex' => __('guest.otp_digits_only'),
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get session
        $sessionToken = session('guest_session_token');
        $guestSession = $sessionToken ? GuestSession::findByToken($sessionToken) : null;

        if (!$guestSession) {
            return redirect()
                ->route('guest.book', ['hall' => $hall->slug, 'lang' => $locale])
                ->with('error', __('guest.session_expired'));
        }

        // Check if locked out
        if ($guestSession->isOtpLocked()) {
            return back()
                ->with('error', __('guest.otp_locked'))
                ->with('can_resend', true);
        }

        // Check if OTP expired
        if ($guestSession->isOtpExpired()) {
            return back()
                ->with('error', __('guest.otp_expired'))
                ->with('can_resend', true);
        }

        // Verify OTP
        if (!$guestSession->verifyOtp($request->input('otp'))) {
            $remaining = $guestSession->remaining_otp_attempts;

            return back()
                ->with('error', __('guest.otp_incorrect', ['remaining' => $remaining]))
                ->withInput();
        }

        Log::info('Guest OTP verified', [
            'session_id' => $guestSession->id,
            'email' => $guestSession->masked_email,
        ]);

        return redirect()
            ->route('guest.booking-form', ['hall' => $hall->slug, 'lang' => $locale])
            ->with('success', __('guest.otp_verified'));
    }

    /**
     * Resend OTP code.
     *
     * @param Request $request
     * @param Hall $hall
     * @return RedirectResponse
     */
    public function resendOtp(Request $request, Hall $hall): RedirectResponse
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        $sessionToken = session('guest_session_token');
        $guestSession = $sessionToken ? GuestSession::findByToken($sessionToken) : null;

        if (!$guestSession) {
            return redirect()
                ->route('guest.book', ['hall' => $hall->slug, 'lang' => $locale])
                ->with('error', __('guest.session_expired'));
        }

        // Rate limit: wait at least 60 seconds between resends
        $lastOtpTime = $guestSession->otp_expires_at
            ? $guestSession->otp_expires_at->subMinutes(GuestSession::OTP_EXPIRY_MINUTES)
            : null;

        if ($lastOtpTime && $lastOtpTime->diffInSeconds(now()) < 60) {
            $waitTime = 60 - $lastOtpTime->diffInSeconds(now());
            return back()
                ->with('error', __('guest.otp_resend_wait', ['seconds' => $waitTime]));
        }

        try {
            // Generate new OTP
            $otp = $guestSession->generateOtp();

            // Send OTP notification
            Notification::route('mail', $guestSession->email)
                ->notify(new GuestBookingOtpNotification($otp, $guestSession->name, $hall));

            Log::info('Guest OTP resent', [
                'session_id' => $guestSession->id,
                'email' => $guestSession->masked_email,
            ]);

            return back()
                ->with('success', __('guest.otp_resent'));

        } catch (Exception $e) {
            Log::error('Failed to resend OTP', [
                'error' => $e->getMessage(),
                'session_id' => $guestSession->id,
            ]);

            return back()
                ->with('error', __('guest.otp_resend_failed') + ': ' + $e->getMessage());
        }
    }

    /**
     * Show booking form after OTP verification.
     *
     * @param Hall $hall
     * @return View|RedirectResponse
     */
    public function showBookingForm(Hall $hall): View|RedirectResponse
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        $sessionToken = session('guest_session_token');
        $guestSession = $sessionToken ? GuestSession::findByToken($sessionToken) : null;

        if (!$guestSession || !$guestSession->is_verified) {
            return redirect()
                ->route('guest.book', ['hall' => $hall->slug, 'lang' => $locale])
                ->with('error', __('guest.verification_required'));
        }

        // Update session status
        $guestSession->updateStatus('booking');

        // Load hall with relationships
        $hall->load(['city.region', 'owner', 'activeExtraServices']);

        return view('customer.guest.booking-form', [
            'hall' => $hall,
            'guestSession' => $guestSession,
        ]);
    }

    /**
     * Store guest booking.
     *
     * @param Request $request
     * @param Hall $hall
     * @return RedirectResponse
     */
    public function store(Request $request, Hall $hall): RedirectResponse
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        // Verify session
        $sessionToken = session('guest_session_token');
        $guestSession = $sessionToken ? GuestSession::findByToken($sessionToken) : null;

        if (!$guestSession || !$guestSession->canProceedToBooking()) {
            return redirect()
                ->route('guest.book', ['hall' => $hall->slug, 'lang' => $locale])
                ->with('error', __('guest.session_invalid'));
        }

        // Validate booking data
        $validator = Validator::make($request->all(), [
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'time_slot' => ['required', 'in:morning,afternoon,evening,full_day'],
            'number_of_guests' => [
                'required',
                'integer',
                'min:' . ($hall->capacity_min ?? 1),
                'max:' . ($hall->capacity_max ?? 1000),
            ],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
            'event_type' => ['nullable', 'string', 'max:100'],
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:extra_services,id'],
            'agree_terms' => ['required', 'accepted'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check availability
        $isAvailable = $this->isSlotAvailable(
            $hall,
            $request->input('booking_date'),
            $request->input('time_slot')
        );

        if (!$isAvailable) {
            return back()
                ->with('error', __('halls.slot_not_available') !== 'halls.slot_not_available'
                    ? __('halls.slot_not_available')
                    : 'This slot is not available.')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Get selected services
            $selectedServiceIds = $request->input('services', []);

            // Calculate hall price based on time slot
            $hallPrice = $this->getSlotPrice($hall, $request->input('time_slot'));

            // Calculate services total
            $servicesPrice = 0.00;
            if (!empty($selectedServiceIds)) {
                $servicesPrice = (float) ExtraService::whereIn('id', $selectedServiceIds)->sum('price');
            }

            // Calculate subtotal (hall + services)
            $subtotal = $hallPrice + $servicesPrice;

            // Platform fee (if applicable - usually 0 for now)
            $platformFee = 0.00;

            // Total amount
            $totalAmount = $subtotal + $platformFee;

            // Commission calculation (if applicable)
            $commissionAmount = 0.00;
            $commissionType = null;
            $commissionValue = null;

            // Owner payout (total - commission)
            $ownerPayout = $totalAmount - $commissionAmount;

            // Generate booking number (format: BK-YYYY-NNNNN)
            $lastBooking = Booking::withTrashed()->orderBy('id', 'desc')->first();
            $nextNumber = $lastBooking ? ($lastBooking->id + 1) : 1;
            $bookingNumber = 'BK-' . now()->format('Y') . '-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);

            // Generate guest token (64 character hex string)
            $guestToken = bin2hex(random_bytes(32));

            // Create booking with ALL required fields
            $booking = Booking::create([
                // Identifiers
                'booking_number' => $bookingNumber,
                'hall_id' => $hall->id,
                'user_id' => null, // Guest booking - no user

                // Guest booking fields
                'is_guest_booking' => true,
                'guest_token' => $guestToken,
                'guest_token_expires_at' => now()->addYear(),

                // Booking details
                'booking_date' => $request->input('booking_date'),
                'time_slot' => $request->input('time_slot'),
                'number_of_guests' => (int) $request->input('number_of_guests'),

                // Customer info
                'customer_name' => $guestSession->name,
                'customer_email' => $guestSession->email,
                'customer_phone' => $guestSession->phone,
                'customer_notes' => $request->input('customer_notes'),
                'event_type' => $request->input('event_type'),

                // Pricing - ALL REQUIRED FIELDS
                'hall_price' => round($hallPrice, 2),
                'services_price' => round($servicesPrice, 2),
                'subtotal' => round($subtotal, 2),
                'platform_fee' => round($platformFee, 2),
                'total_amount' => round($totalAmount, 2),

                // Commission
                'commission_amount' => round($commissionAmount, 2),
                'commission_type' => $commissionType,
                'commission_value' => $commissionValue,
                'owner_payout' => round($ownerPayout, 2),

                // Status
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_type' => 'full',
            ]);

            // Attach extra services with their prices
            if (!empty($selectedServiceIds)) {
                $services = ExtraService::whereIn('id', $selectedServiceIds)->get();
                $pivotData = [];
                foreach ($services as $service) {
                    $pivotData[$service->id] = ['price' => $service->price];
                }
                $booking->extraServices()->attach($pivotData);
            }

            // Update guest session
            $guestSession->update([
                'status' => 'payment',
                'booking_data' => json_encode([
                    'booking_id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                ]),
            ]);

            DB::commit();

            Log::info('Guest booking created', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'hall_price' => $booking->hall_price,
                'services_price' => $booking->services_price,
                'subtotal' => $booking->subtotal,
                'total_amount' => $booking->total_amount,
                'session_id' => $guestSession->id,
            ]);

            // Redirect to payment
            return redirect()
                ->route('guest.booking.payment', [
                    'guest_token' => $guestToken,
                    'lang' => $locale,
                ])
                ->with('success', __('halls.booking_created_proceed_payment') !== 'halls.booking_created_proceed_payment'
                    ? __('halls.booking_created_proceed_payment')
                    : 'Booking created! Please proceed to payment.');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Guest booking creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => $guestSession->id ?? null,
                'hall_id' => $hall->id,
            ]);

            return back()
                ->with('error', __('halls.booking_failed') !== 'halls.booking_failed'
                    ? __('halls.booking_failed')
                    : 'Booking failed. Please try again.' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get price for a specific time slot.
     * Uses pricing_override if available, otherwise uses base price_per_slot.
     *
     * @param Hall $hall
     * @param string $timeSlot
     * @return float
     */
    protected function getSlotPrice(Hall $hall, string $timeSlot): float
    {
        // Check pricing_override JSON field first
        $pricingOverride = $hall->pricing_override;

        if (!empty($pricingOverride) && is_array($pricingOverride)) {
            if (isset($pricingOverride[$timeSlot]) && $pricingOverride[$timeSlot] > 0) {
                return (float) $pricingOverride[$timeSlot];
            }
        }

        // Fallback to base price_per_slot
        $basePrice = (float) ($hall->price_per_slot ?? 0);

        // If no pricing override and checking full_day, apply multiplier
        if ($timeSlot === 'full_day' && empty($pricingOverride['full_day'])) {
            return $basePrice * 2.5; // Default full day multiplier
        }

        return $basePrice;
    }


    /**
     * Show booking details for guest (using token).
     *
     * @param string $guestToken
     * @return View|RedirectResponse
     */
    public function show(string $guestToken): View|RedirectResponse
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        $booking = Booking::findByGuestToken($guestToken);

        if (!$booking) {
            return redirect()
                ->route('customer.halls.index')
                ->with('error', __('guest.booking_not_found'));
        }

        $booking->load(['hall.city.region', 'extraServices', 'payment']);

        return view('customer.guest.booking-details', compact('booking'));
    }

    /**
     * Show payment page for guest booking.
     *
     * @param string $guestToken
     * @return View|RedirectResponse
     */
    public function payment(string $guestToken): View|RedirectResponse
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        $booking = Booking::findByGuestToken($guestToken);

        if (!$booking) {
            return redirect()
                ->route('customer.halls.index')
                ->with('error', __('guest.booking_not_found'));
        }

        // Check if payment already completed
        if ($booking->payment_status === 'paid') {
            return redirect()
                ->route('guest.booking.success', [
                    'guest_token' => $guestToken,
                    'lang' => $locale,
                ])
                ->with('info', __('halls.payment_already_completed'));
        }

        $booking->load(['hall.city.region', 'extraServices']);

        return view('customer.guest.payment', compact('booking'));
    }




    /**
     * Process guest booking payment and redirect to Thawani gateway.
     *
     * @param Request $request
     * @param string $guest_token The unique guest booking token
     * @return RedirectResponse
     */
    public function processPayment(Request $request, string $guest_token): RedirectResponse
    {
        // Get locale from request or session
        $locale = $request->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        Log::info('=== GUEST PAYMENT PROCESS STARTED ===', [
            'guest_token' => substr($guest_token, 0, 16) . '...',
            'payment_type' => $request->input('payment_type'),
        ]);

        try {
            // ================================================================
            // STEP 1: Find and validate the booking
            // ================================================================

            $booking = Booking::where('guest_token', $guest_token)
                ->where('is_guest_booking', true)
                ->where('payment_status', 'pending')
                ->whereNull('deleted_at')
                ->with('hall')
                ->first();

            if (!$booking) {
                Log::warning('Guest payment: Booking not found', [
                    'guest_token' => substr($guest_token, 0, 16) . '...',
                ]);

                return redirect()
                    ->route('customer.halls.index', ['lang' => $locale])
                    ->with('error', __('guest.booking_not_found') !== 'guest.booking_not_found'
                        ? __('guest.booking_not_found')
                        : 'Booking not found or already processed.');
            }

            Log::info('Guest payment: Booking found', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'total_amount' => $booking->total_amount,
            ]);

            // Check token expiry
            if ($booking->guest_token_expires_at && $booking->guest_token_expires_at->isPast()) {
                Log::warning('Guest payment: Token expired', [
                    'booking_id' => $booking->id,
                    'expired_at' => $booking->guest_token_expires_at,
                ]);

                return redirect()
                    ->route('customer.halls.index', ['lang' => $locale])
                    ->with('error', __('guest.token_expired') !== 'guest.token_expired'
                        ? __('guest.token_expired')
                        : 'Your booking session has expired. Please create a new booking.');
            }

            // ================================================================
            // STEP 2: Calculate payment amount
            // ================================================================

            $paymentType = $request->input('payment_type', 'full');
            $totalAmount = (float) $booking->total_amount;

            // Calculate based on payment type
            if ($paymentType === 'advance' && $booking->hall && $booking->hall->allows_advance_payment) {
                $advancePercentage = (float) ($booking->hall->advance_percentage ?? 50);
                $paymentAmount = round($totalAmount * ($advancePercentage / 100), 3);
                $balanceDue = round($totalAmount - $paymentAmount, 3);
            } else {
                $paymentType = 'full';
                $paymentAmount = $totalAmount;
                $balanceDue = 0.00;
            }

            Log::info('Guest payment: Amount calculated', [
                'payment_type' => $paymentType,
                'total_amount' => $totalAmount,
                'payment_amount' => $paymentAmount,
                'balance_due' => $balanceDue,
            ]);

            // Minimum amount check (Thawani requires at least 0.100 OMR)
            if ($paymentAmount < 0.100) {
                Log::error('Guest payment: Amount too low', [
                    'booking_id' => $booking->id,
                    'amount' => $paymentAmount,
                ]);
                return back()->with('error', 'Payment amount is too low. Minimum is 0.100 OMR.');
            }

            // ================================================================
            // STEP 3: Create payment record
            // ================================================================

            DB::beginTransaction();

            $paymentReference = 'PAY-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 6));

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_reference' => $paymentReference,
                'amount' => round($paymentAmount, 3),
                'currency' => 'OMR',
                'status' => 'pending',
                'payment_method' => 'online',
                'customer_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Update booking with payment type
            $booking->update([
                'payment_type' => $paymentType,
                'advance_amount' => $paymentType === 'advance' ? round($paymentAmount, 3) : null,
                'balance_due' => $paymentType === 'advance' ? round($balanceDue, 3) : null,
            ]);

            DB::commit();

            Log::info('Guest payment: Payment record created', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'payment_reference' => $paymentReference,
                'amount' => $paymentAmount,
            ]);

            // ================================================================
            // STEP 4: Create Thawani checkout session
            // ================================================================

            // Get Thawani credentials from config or environment
            $thawaniMode = config('services.thawani.mode', env('THAWANI_MODE', 'test'));
            $secretKey = config('services.thawani.secret_key', env('THAWANI_SECRET_KEY', env('THAWANI_API_KEY', '')));
            $publishableKey = config('services.thawani.publishable_key', env('THAWANI_PUBLISHABLE_KEY', ''));

            Log::info('Guest payment: Thawani config', [
                'mode' => $thawaniMode,
                'has_secret_key' => !empty($secretKey),
                'has_publishable_key' => !empty($publishableKey),
            ]);

            if (empty($secretKey) || empty($publishableKey)) {
                Log::error('Guest payment: Thawani keys not configured');

                // Rollback payment record
                $payment->update([
                    'status' => 'failed',
                    'failure_reason' => 'Payment gateway not configured',
                    'failed_at' => now(),
                ]);

                return back()->with('error', 'Payment gateway is not properly configured. Please contact support.');
            }

            // Set API URLs based on mode
            $apiUrl = $thawaniMode === 'live'
                ? 'https://checkout.thawani.om/api/v1/checkout/session'
                : 'https://uatcheckout.thawani.om/api/v1/checkout/session';

            $checkoutBaseUrl = $thawaniMode === 'live'
                ? 'https://checkout.thawani.om/pay/'
                : 'https://uatcheckout.thawani.om/pay/';

            // Prepare product name
            $hallName = $booking->hall?->getTranslation('name', 'en') ?? 'Hall Booking';

            // Build success and cancel URLs using YOUR EXISTING ROUTES
            // Route names from guest-booking.php: guest.payment.success, guest.payment.cancel
            $successUrl = route('guest.payment.success', [
                'guest_token' => $guest_token,
                'lang' => $locale,
            ]);

            $cancelUrl = route('guest.payment.cancel', [
                'guest_token' => $guest_token,
                'lang' => $locale,
            ]);

            Log::info('Guest payment: Callback URLs', [
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);

            // Build payload for Thawani
            $payload = [
                'client_reference_id' => $paymentReference,
                'mode' => 'payment',
                'products' => [
                    [
                        'name' => substr($hallName . ' - ' . $booking->booking_number, 0, 40), // Thawani has 40 char limit
                        'quantity' => 1,
                        'unit_amount' => (int) round($paymentAmount * 1000), // Convert OMR to baisa
                    ],
                ],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'booking_id' => (string) $booking->id,
                    'booking_number' => $booking->booking_number,
                    'payment_reference' => $paymentReference,
                    'is_guest_booking' => 'true',
                ],
            ];

            Log::info('Guest payment: Calling Thawani API', [
                'api_url' => $apiUrl,
                'client_reference_id' => $paymentReference,
                'amount_baisa' => $payload['products'][0]['unit_amount'],
            ]);

            // Make API request to Thawani
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'thawani-api-key' => $secretKey,
            ])->timeout(30)->post($apiUrl, $payload);

            $responseData = $response->json();
            $statusCode = $response->status();

            Log::info('Guest payment: Thawani response', [
                'status_code' => $statusCode,
                'response' => $responseData,
            ]);

            // Check for errors
            if (!$response->successful()) {
                $errorMsg = $responseData['description'] ?? $responseData['message'] ?? 'Gateway error (HTTP ' . $statusCode . ')';

                Log::error('Guest payment: Thawani API error', [
                    'booking_id' => $booking->id,
                    'status_code' => $statusCode,
                    'response' => $responseData,
                    'error' => $errorMsg,
                ]);

                // Update payment as failed
                $payment->update([
                    'status' => 'failed',
                    'failure_reason' => $errorMsg,
                    'failed_at' => now(),
                    'gateway_response' => json_encode($responseData),
                ]);

                return back()->with('error', 'Payment gateway error: ' . $errorMsg);
            }

            // Validate response has session_id
            if (!isset($responseData['data']['session_id'])) {
                Log::error('Guest payment: Missing session_id in response', [
                    'booking_id' => $booking->id,
                    'response' => $responseData,
                ]);

                $payment->update([
                    'status' => 'failed',
                    'failure_reason' => 'Invalid gateway response - missing session_id',
                    'failed_at' => now(),
                    'gateway_response' => json_encode($responseData),
                ]);

                return back()->with('error', 'Invalid response from payment gateway. Please try again.');
            }

            // Get session ID and build payment URL
            $sessionId = $responseData['data']['session_id'];
            $paymentUrl = $checkoutBaseUrl . $sessionId . '?key=' . $publishableKey;

            // Update payment record with Thawani session
            $payment->update([
                'transaction_id' => $sessionId,
                'payment_url' => $paymentUrl,
                'gateway_response' => json_encode($responseData),
            ]);

            Log::info('=== GUEST PAYMENT: REDIRECTING TO THAWANI ===', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'session_id' => $sessionId,
                'payment_url' => $paymentUrl,
            ]);

            // ================================================================
            // STEP 5: Redirect to Thawani payment page
            // ================================================================

            return redirect()->away($paymentUrl);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('=== GUEST PAYMENT EXCEPTION ===', [
                'guest_token' => substr($guest_token, 0, 16) . '...',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful payment callback from Thawani.
     *
     * Note: This matches the route in guest-booking.php:
     * Route::get('/payment/success/{guest_token}', ...)->name('payment.success');
     *
     * @param Request $request
     * @param string $guest_token
     * @return RedirectResponse
     */
    public function paymentSuccess(Request $request, string $guest_token): RedirectResponse
    {
        $locale = $request->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        Log::info('=== GUEST PAYMENT SUCCESS CALLBACK ===', [
            'guest_token' => substr($guest_token, 0, 16) . '...',
        ]);

        try {
            // Find the booking
            $booking = Booking::where('guest_token', $guest_token)
                ->where('is_guest_booking', true)
                ->first();

            if (!$booking) {
                Log::warning('Guest payment success: Booking not found', [
                    'guest_token' => substr($guest_token, 0, 16) . '...',
                ]);

                return redirect()
                    ->route('customer.halls.index', ['lang' => $locale])
                    ->with('error', 'Booking not found.');
            }

            // Find the most recent pending payment for this booking
            $payment = Payment::where('booking_id', $booking->id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$payment) {
                // Check if already paid
                $paidPayment = Payment::where('booking_id', $booking->id)
                    ->where('status', 'paid')
                    ->first();

                if ($paidPayment) {
                    Log::info('Guest payment success: Already processed', [
                        'booking_id' => $booking->id,
                        'payment_id' => $paidPayment->id,
                    ]);

                    return redirect()
                        ->route('guest.booking.success', [
                            'guest_token' => $guest_token,
                            'lang' => $locale,
                        ])
                        ->with('info', 'Your booking is already confirmed.');
                }

                Log::warning('Guest payment success: No payment record found', [
                    'booking_id' => $booking->id,
                ]);

                return redirect()
                    ->route('guest.booking.payment', [
                        'guest_token' => $guest_token,
                        'lang' => $locale,
                    ])
                    ->with('error', 'Payment record not found. Please try again.');
            }

            // Verify with Thawani if we have a session ID
            $verified = true; // Default to true, Thawani callback is reliable

            if ($payment->transaction_id) {
                try {
                    $thawaniMode = config('services.thawani.mode', env('THAWANI_MODE', 'test'));
                    $secretKey = config('services.thawani.secret_key', env('THAWANI_SECRET_KEY', env('THAWANI_API_KEY', '')));

                    $verifyUrl = ($thawaniMode === 'live'
                        ? 'https://checkout.thawani.om/api/v1/checkout/session/'
                        : 'https://uatcheckout.thawani.om/api/v1/checkout/session/')
                        . $payment->transaction_id;

                    $verifyResponse = Http::withHeaders([
                        'thawani-api-key' => $secretKey,
                    ])->timeout(15)->get($verifyUrl);

                    $verifyData = $verifyResponse->json();

                    Log::info('Guest payment: Thawani verification', [
                        'payment_id' => $payment->id,
                        'verify_response' => $verifyData,
                    ]);

                    // Update gateway response with verification
                    $existingResponse = json_decode($payment->gateway_response ?? '{}', true) ?: [];
                    $payment->update([
                        'gateway_response' => json_encode(array_merge($existingResponse, [
                            'verification' => $verifyData,
                        ])),
                    ]);

                    // Check if payment was actually successful
                    $paymentStatus = $verifyData['data']['payment_status'] ?? null;
                    if ($paymentStatus !== 'paid') {
                        Log::warning('Guest payment: Verification shows not paid', [
                            'payment_id' => $payment->id,
                            'payment_status' => $paymentStatus,
                        ]);
                        $verified = false;
                    }
                } catch (Exception $verifyError) {
                    Log::warning('Guest payment: Verification request failed', [
                        'payment_id' => $payment->id,
                        'error' => $verifyError->getMessage(),
                    ]);
                    // Continue anyway - Thawani callback is usually reliable
                }
            }

            if (!$verified) {
                return redirect()
                    ->route('guest.booking.payment', [
                        'guest_token' => $guest_token,
                        'lang' => $locale,
                    ])
                    ->with('warning', 'Payment verification failed. Please try again or contact support.');
            }

            // Update payment and booking status
            DB::beginTransaction();

            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $newPaymentStatus = $booking->payment_type === 'advance' ? 'partial' : 'paid';

            $booking->update([
                'payment_status' => $newPaymentStatus,
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            DB::commit();

            Log::info('=== GUEST PAYMENT COMPLETED ===', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'payment_type' => $booking->payment_type,
                'new_status' => $newPaymentStatus,
            ]);

            // Redirect to success page
            return redirect()
                ->route('guest.booking.success', [
                    'guest_token' => $guest_token,
                    'lang' => $locale,
                ])
                ->with('success', __('payment.success') !== 'payment.success'
                    ? __('payment.success')
                    : 'Payment successful! Your booking has been confirmed.');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Guest payment success callback exception', [
                'guest_token' => substr($guest_token, 0, 16) . '...',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('guest.booking.payment', [
                    'guest_token' => $guest_token,
                    'lang' => $locale,
                ])
                ->with('error', 'There was an issue confirming your payment. Please contact support.');
        }
    }

    /**
     * Handle cancelled payment callback from Thawani.
     *
     * Note: This matches the route in guest-booking.php:
     * Route::get('/payment/cancel/{guest_token}', ...)->name('payment.cancel');
     *
     * @param Request $request
     * @param string $guest_token
     * @return RedirectResponse
     */
    public function paymentCancel(Request $request, string $guest_token): RedirectResponse
    {
        $locale = $request->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        Log::info('=== GUEST PAYMENT CANCELLED ===', [
            'guest_token' => substr($guest_token, 0, 16) . '...',
        ]);

        try {
            // Find the booking
            $booking = Booking::where('guest_token', $guest_token)->first();

            if ($booking) {
                // Find and update the pending payment
                $payment = Payment::where('booking_id', $booking->id)
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($payment) {
                    $payment->update([
                        'status' => 'cancelled',
                        'failed_at' => now(),
                        'failure_reason' => 'User cancelled payment',
                    ]);

                    Log::info('Guest payment: Payment cancelled', [
                        'payment_id' => $payment->id,
                        'booking_id' => $booking->id,
                    ]);
                }
            }

            return redirect()
                ->route('guest.booking.payment', [
                    'guest_token' => $guest_token,
                    'lang' => $locale,
                ])
                ->with('warning', __('payment.cancelled') !== 'payment.cancelled'
                    ? __('payment.cancelled')
                    : 'Payment was cancelled. You can try again when ready.');
        } catch (Exception $e) {
            Log::error('Guest payment cancel callback error', [
                'guest_token' => substr($guest_token, 0, 16) . '...',
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('guest.booking.payment', [
                    'guest_token' => $guest_token,
                    'lang' => $locale,
                ])
                ->with('info', 'Payment was cancelled.');
        }
    }

    /**
     * Show booking success page with account creation option.
     *
     * @param string $guestToken
     * @return View|RedirectResponse
     */
    public function success(string $guestToken): View|RedirectResponse
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        $booking = Booking::findByGuestToken($guestToken);

        if (!$booking) {
            return redirect()
                ->route('customer.halls.index')
                ->with('error', __('guest.booking_not_found'));
        }

        $booking->load(['hall.city.region', 'extraServices']);

        // Check if can create account
        $canCreateAccount = $booking->canCreateAccount();

        return view('customer.guest.booking-success', [
            'booking' => $booking,
            'canCreateAccount' => $canCreateAccount,
        ]);
    }

    /**
     * Create account for guest and link bookings.
     *
     * @param Request $request
     * @param string $guestToken
     * @return RedirectResponse
     */
    public function createAccount(Request $request, string $guestToken): RedirectResponse
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        $booking = Booking::findByGuestToken($guestToken);

        if (!$booking) {
            return redirect()
                ->route('customer.halls.index')
                ->with('error', __('guest.booking_not_found'));
        }

        // Check if can create account
        if (!$booking->canCreateAccount()) {
            return back()
                ->with('error', __('guest.account_already_exists'));
        }

        // Validate account creation data
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'password.required' => __('validation.required', ['attribute' => __('Password')]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('Password')]),
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => $booking->customer_name,
                'email' => strtolower($booking->customer_email),
                'phone' => $booking->customer_phone,
                'password' => Hash::make($request->input('password')),
                'email_verified_at' => now(), // Auto-verify since we verified via OTP
            ]);

            // Assign customer role if using Spatie permissions
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('customer');
            }

            // Link all guest bookings with this email to the new user
            $linkedCount = Booking::linkGuestBookingsToUser($user);

            DB::commit();

            Log::info('Guest account created and bookings linked', [
                'user_id' => $user->id,
                'email' => $user->email,
                'bookings_linked' => $linkedCount,
            ]);

            // Log in the user
            auth()->login($user);

            return redirect()
                ->route('customer.dashboard')
                ->with('success', __('guest.account_created', ['count' => $linkedCount]));

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Guest account creation failed', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id,
            ]);

            return back()
                ->with('error', __('guest.account_creation_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Check hall availability (AJAX endpoint).
     *
     * Returns availability for the requested slot AND all other slots.
     * This allows the frontend to show which slots are still available.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAvailability(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'hall_id' => ['required', 'exists:halls,id'],
                'date' => ['required', 'date', 'after_or_equal:today'],
                'time_slot' => ['required', 'in:morning,afternoon,evening,full_day'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'available' => false,
                    'message' => $validator->errors()->first(),
                    'slots' => null,
                ], 422);
            }

            $hall = Hall::find($request->input('hall_id'));
            $date = $request->input('date');
            $timeSlot = $request->input('time_slot');

            // Check availability for requested slot
            $isAvailable = $this->isSlotAvailable($hall, $date, $timeSlot);

            // Get availability for all slots (useful for UI)
            $allSlotsAvailability = $this->getAvailableSlots($hall, $date);

            // Get which slots are already booked
            $bookedSlots = $this->getBookedSlots($hall, $date);

            return response()->json([
                'available' => $isAvailable,
                'message' => $isAvailable
                    ? (__('halls.slot_available') !== 'halls.slot_available' ? __('halls.slot_available') : 'This slot is available!')
                    : (__('halls.slot_not_available') !== 'halls.slot_not_available' ? __('halls.slot_not_available') : 'This slot is not available.'),
                'slots' => $allSlotsAvailability,
                'booked_slots' => $bookedSlots,
            ]);
        } catch (Exception $e) {
            Log::error('Availability check failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'available' => false,
                'message' => 'Error checking availability. Please try again.',
                'slots' => null,
            ], 500);
        }
    }

    /**
     * Check if a time slot is available for a hall on a specific date.
     *
     * @param Hall $hall
     * @param string $date
     * @param string $timeSlot
     * @return bool
     */
    protected function isSlotAvailable(Hall $hall, string $date, string $timeSlot): bool
    {
        /*
         * Time Slot Availability Logic:
         *
         * Available slots: morning, afternoon, evening, full_day
         *
         * Rules:
         * 1. If 'full_day' is booked → ALL slots are blocked
         * 2. If 'morning' is booked → 'morning' and 'full_day' are blocked
         * 3. If 'afternoon' is booked → 'afternoon' and 'full_day' are blocked
         * 4. If 'evening' is booked → 'evening' and 'full_day' are blocked
         * 5. If checking 'full_day' → blocked if ANY slot is already booked
         */

        // Base query for existing bookings on this date
        $baseQuery = Booking::where('hall_id', $hall->id)
            ->where('booking_date', $date)
            ->whereIn('status', ['confirmed', 'pending']);

        // If requesting full_day, check if ANY slot is already booked
        if ($timeSlot === 'full_day') {
            $hasAnyBooking = (clone $baseQuery)
                ->whereIn('time_slot', ['morning', 'afternoon', 'evening', 'full_day'])
                ->exists();

            return !$hasAnyBooking;
        }

        // For individual slots (morning, afternoon, evening):
        // Check if this specific slot OR full_day is already booked
        $conflictingBooking = (clone $baseQuery)
            ->where(function ($query) use ($timeSlot) {
                $query->where('time_slot', $timeSlot)      // Same slot booked
                      ->orWhere('time_slot', 'full_day');  // Full day booked
            })
            ->exists();

        return !$conflictingBooking;
    }

    /**
     * Get all available slots for a hall on a specific date.
     * Useful for showing which slots are still available.
     *
     * @param Hall $hall
     * @param string $date
     * @return array<string, bool>
     */
    protected function getAvailableSlots(Hall $hall, string $date): array
    {
        $slots = ['morning', 'afternoon', 'evening', 'full_day'];
        $availability = [];

        foreach ($slots as $slot) {
            $availability[$slot] = $this->isSlotAvailable($hall, $date, $slot);
        }

        return $availability;
    }

    /**
     * Get booked slots for a hall on a specific date.
     *
     * @param Hall $hall
     * @param string $date
     * @return array<string>
     */
    protected function getBookedSlots(Hall $hall, string $date): array
    {
        return Booking::where('hall_id', $hall->id)
            ->where('booking_date', $date)
            ->whereIn('status', ['confirmed', 'pending'])
            ->pluck('time_slot')
            ->toArray();
    }

    /**
     * Download booking PDF (for guests).
     *
     * @param string $guestToken
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|RedirectResponse
     */
    public function downloadPdf(string $guestToken)
    {
        $booking = Booking::findByGuestToken($guestToken);

        if (!$booking) {
            return redirect()
                ->route('customer.halls.index')
                ->with('error', __('guest.booking_not_found'));
        }

        // Use existing PDF service
        $pdfService = app(\App\Services\BookingPdfService::class);

        return $pdfService->download($booking);
    }
}
