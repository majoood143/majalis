<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\GuestSession;
use App\Models\Hall;
use App\Models\User;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Notifications\GuestBookingOtpNotification;
use App\Notifications\GuestBookingConfirmationNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Exception;

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
                ->with('error', __('guest.initiation_failed'))
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
                ->with('error', __('guest.otp_resend_failed'));
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

        // Get booking service
        $bookingService = app(BookingService::class);

        // Validate booking data
        $validator = Validator::make($request->all(), [
            'booking_date' => ['required', 'date', 'after:today'],
            'time_slot' => ['required', 'in:morning,afternoon,evening,full_day'],
            'number_of_guests' => [
                'required',
                'integer',
                'min:' . $hall->capacity_min,
                'max:' . $hall->capacity_max,
            ],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
            'event_type' => ['nullable', 'string', 'in:wedding,corporate,birthday,conference,graduation,other'],
            'event_details' => ['nullable', 'string', 'max:1000'],
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
        $isAvailable = $bookingService->checkAvailability(
            $hall,
            $request->input('booking_date'),
            $request->input('time_slot')
        );

        if (!$isAvailable) {
            return back()
                ->with('error', __('halls.slot_not_available'))
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Prepare booking data
            $bookingData = [
                'hall_id' => $hall->id,
                'user_id' => null, // Guest booking - no user
                'is_guest_booking' => true,
                'booking_date' => $request->input('booking_date'),
                'time_slot' => $request->input('time_slot'),
                'number_of_guests' => (int) $request->input('number_of_guests'),
                'customer_name' => $guestSession->name,
                'customer_email' => $guestSession->email,
                'customer_phone' => $guestSession->phone,
                'customer_notes' => $request->input('customer_notes'),
                'event_type' => $request->input('event_type'),
                'event_details' => $request->input('event_details'),
            ];

            // Create booking using service
            $booking = $bookingService->createBooking(
                $hall,
                $bookingData,
                $request->input('services', [])
            );

            // Update session with booking reference
            $guestSession->updateStatus('payment');
            $guestSession->storeBookingData([
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
            ]);

            DB::commit();

            Log::info('Guest booking created', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'session_id' => $guestSession->id,
                'guest_email' => $guestSession->masked_email,
            ]);

            // Redirect to payment
            return redirect()
                ->route('guest.booking.payment', [
                    'guest_token' => $booking->guest_token,
                    'lang' => $locale,
                ])
                ->with('success', __('halls.booking_created_proceed_payment'));

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Guest booking creation failed', [
                'error' => $e->getMessage(),
                'session_id' => $guestSession->id,
                'hall_id' => $hall->id,
            ]);

            return back()
                ->with('error', __('halls.booking_failed') . ': ' . $e->getMessage())
                ->withInput();
        }
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
     * Process payment for guest booking.
     *
     * @param Request $request
     * @param string $guestToken
     * @return RedirectResponse
     */
    public function processPayment(Request $request, string $guestToken): RedirectResponse
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        $booking = Booking::findByGuestToken($guestToken);

        if (!$booking) {
            return redirect()
                ->route('customer.halls.index')
                ->with('error', __('guest.booking_not_found'));
        }

        try {
            $paymentService = app(PaymentService::class);

            // Determine payment amount based on hall settings
            $hall = $booking->hall;
            $paymentType = 'full';
            $paymentAmount = $booking->total_amount;

            if ($hall->allows_advance_payment && $request->input('payment_type') === 'advance') {
                $paymentType = 'advance';
                $paymentAmount = $booking->calculateAdvanceAmount();
            }

            // Create payment with guest-specific callback URLs
            $payment = $paymentService->createPayment(
                $booking,
                (float) $paymentAmount,
                [
                    'success_url' => route('guest.payment.success', ['guest_token' => $guestToken]),
                    'cancel_url' => route('guest.payment.cancel', ['guest_token' => $guestToken]),
                    'payment_type' => $paymentType,
                ]
            );

            // Update booking payment type
            $booking->update(['payment_type' => $paymentType]);

            Log::info('Guest payment initiated', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'amount' => $paymentAmount,
            ]);

            // Redirect to payment gateway
            return redirect()->away($payment->payment_url);

        } catch (Exception $e) {
            Log::error('Guest payment processing failed', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id,
            ]);

            return back()
                ->with('error', __('halls.payment_processing_failed'));
        }
    }

    /**
     * Handle successful payment callback.
     *
     * @param Request $request
     * @param string $guestToken
     * @return RedirectResponse
     */
    public function paymentSuccess(Request $request, string $guestToken): RedirectResponse
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        $booking = Booking::findByGuestToken($guestToken);

        if (!$booking) {
            return redirect()
                ->route('customer.halls.index')
                ->with('error', __('guest.booking_not_found'));
        }

        try {
            $paymentService = app(PaymentService::class);

            // Verify payment with gateway
            $sessionId = $request->query('session_id');
            if ($sessionId && $booking->payment) {
                $paymentService->verifyPayment($booking->payment, $sessionId);
            }

            // Update guest session if exists
            $guestSession = $booking->guestSession;
            if ($guestSession) {
                $guestSession->complete($booking);
            }

            // Send confirmation notification
            Notification::route('mail', $booking->customer_email)
                ->notify(new GuestBookingConfirmationNotification($booking));

            // Clear browser session
            session()->forget('guest_session_token');

            Log::info('Guest payment successful', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
            ]);

            return redirect()
                ->route('guest.booking.success', [
                    'guest_token' => $guestToken,
                    'lang' => $locale,
                ])
                ->with('success', __('halls.payment_successful'));

        } catch (Exception $e) {
            Log::error('Guest payment verification failed', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id,
            ]);

            return redirect()
                ->route('guest.booking.show', [
                    'guest_token' => $guestToken,
                    'lang' => $locale,
                ])
                ->with('warning', __('halls.payment_verification_pending'));
        }
    }

    /**
     * Handle cancelled payment callback.
     *
     * @param string $guestToken
     * @return RedirectResponse
     */
    public function paymentCancel(string $guestToken): RedirectResponse
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        $booking = Booking::findByGuestToken($guestToken);

        if (!$booking) {
            return redirect()
                ->route('customer.halls.index')
                ->with('error', __('guest.booking_not_found'));
        }

        Log::info('Guest payment cancelled', [
            'booking_id' => $booking->id,
        ]);

        return redirect()
            ->route('guest.booking.payment', [
                'guest_token' => $guestToken,
                'lang' => $locale,
            ])
            ->with('warning', __('halls.payment_cancelled'));
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
                ->with('error', __('guest.account_creation_failed'));
        }
    }

    /**
     * Check hall availability (AJAX endpoint).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hall_id' => ['required', 'exists:halls,id'],
            'date' => ['required', 'date', 'after:today'],
            'time_slot' => ['required', 'in:morning,afternoon,evening,full_day'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'available' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $hall = Hall::find($request->input('hall_id'));
        $bookingService = app(BookingService::class);

        $isAvailable = $bookingService->checkAvailability(
            $hall,
            $request->input('date'),
            $request->input('time_slot')
        );

        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable
                ? __('halls.slot_available')
                : __('halls.slot_not_available'),
        ]);
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
