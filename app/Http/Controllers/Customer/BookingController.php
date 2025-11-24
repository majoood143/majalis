<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use Illuminate\Routing\Controller as BaseController;
use App\Models\Booking;
use App\Models\Hall;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Services\BookingPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Controller for handling customer booking operations
 *
 * @package App\Http\Controllers\Customer
 */
class BookingController extends BaseController
{
    /**
     * BookingController constructor
     * Apply authentication middleware
     */
    public function __construct()
    {
        // Apply auth middleware to all methods except checkAvailability
        $this->middleware('auth')->except(['checkAvailability']);
    }

    /**
     * Show the booking form for a specific hall
     *
     * @param Hall $hall
     * @return \Illuminate\View\View
     */
    public function create(Hall $hall)
    {
        // Set locale from query parameter or session
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);
        session(['locale' => $locale]);

        // Check if hall is active
        if (!$hall->is_active) {
            abort(404, 'Hall not found or inactive');
        }

        // Load necessary relationships
        $hall->load(['city.region', 'owner', 'activeExtraServices']);

        return view('customer.book', compact('hall'));
    }

    /**
     * Store a new booking
     *
     * @param Request $request
     * @param Hall $hall
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Hall $hall)
    {
        // Set locale
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        // Get services from container
        $bookingService = app(BookingService::class);

        // Validation
        $validator = Validator::make($request->all(), [
            'booking_date' => 'required|date|after:today',
            'time_slot' => 'required|in:morning,afternoon,evening,full_day',
            'number_of_guests' => "required|integer|min:{$hall->capacity_min}|max:{$hall->capacity_max}",
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_notes' => 'nullable|string|max:1000',
            'event_type' => 'nullable|string|in:wedding,corporate,birthday,conference,graduation,other',
            'event_details' => 'nullable|string|max:1000',
            'services' => 'nullable|array',
            'services.*' => 'exists:extra_services,id',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', __('halls.invalid_request'));
        }

        try {
            // Check availability using the service
            if (!$bookingService->checkAvailability(
                $hall->id,
                $request->booking_date,
                $request->time_slot
            )) {
                return back()
                    ->with('error', __('halls.date_not_available'))
                    ->withInput();
            }

            // Prepare booking data for the service
            $bookingData = [
                'hall_id' => $hall->id,
                'user_id' => Auth::id(),
                'booking_date' => $request->booking_date,
                'time_slot' => $request->time_slot,
                'number_of_guests' => $request->number_of_guests,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_notes' => $request->customer_notes,
                'event_type' => $request->event_type,
                'event_details' => $request->event_details,
                'extra_services' => $request->services ?? [],
            ];

            // Create booking using the service
            $booking = $bookingService->createBooking($bookingData);

            // Redirect based on payment amount
            if ($booking->total_amount > 0) {
                return redirect()
                    ->route('customer.booking.payment', ['booking' => $booking->id, 'lang' => $locale])
                    ->with('success', __('halls.booking_success'));
            }

            // No payment needed, go directly to success page
            return redirect()
                ->route('customer.booking.success', ['bookingNumber' => $booking->booking_number, 'lang' => $locale])
                ->with('success', __('halls.booking_success'));
        } catch (Exception $e) {
            Log::error('Booking creation failed: ' . $e->getMessage(), [
                'hall_id' => $hall->id,
                'user_id' => Auth::id(),
                'exception' => $e
            ]);

            return back()
                ->with('error', __('halls.booking_failed') . ' ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show booking success page
     *
     * @param string $bookingNumber
     * @return \Illuminate\View\View
     */
    public function success(string $bookingNumber)
    {
        // Set locale
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        // Find booking by booking_number (not booking_reference)
        $booking = Booking::with(['hall.city.region', 'extraServices', 'user'])
            ->where('booking_number', $bookingNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('customer.booking-success', compact('booking'));
    }

    /**
     * Show user's bookings list
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Set locale
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        $bookings = Booking::with(['hall.city'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    /**
     * Show specific booking details
     *
     * @param Booking $booking
     * @return \Illuminate\View\View
     */
    public function show(Booking $booking)
    {
        // Set locale
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking');
        }

        $booking->load(['hall.city.region', 'extraServices', 'user']);

        return view('customer.bookings.show', compact('booking'));
    }

    /**
     * Show payment page
     *
     * @param Booking $booking
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function payment(Booking $booking)
    {
        // Set locale
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking');
        }

        // Check if already paid
        if ($booking->payment_status === 'paid') {
            return redirect()
                ->route('customer.booking.success', ['reference' => $booking->booking_reference, 'lang' => $locale])
                ->with('info', __('halls.already_paid'));
        }

        $booking->load(['hall.city', 'extraServices']);

        return view('customer.payment', compact('booking'));
    }

    /**
     * Process payment
     *
     * @param Request $request
     * @param Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    // public function processPayment(Request $request, Booking $booking)
    // {
    //     // Set locale
    //     $locale = request()->get('lang', session('locale', 'ar'));
    //     app()->setLocale($locale);

    //     // Validate payment method
    //     $request->validate([
    //         'payment_method' => 'required|in:online,bank_transfer,cash',
    //     ]);

    //     $paymentService = app(PaymentService::class);

    //     // Ensure user owns this booking
    //     if ($booking->user_id !== Auth::id()) {
    //         abort(403, 'Unauthorized access to booking');
    //     }

    //     // Check if already paid
    //     if ($booking->payment_status === 'paid') {
    //         return redirect()
    //             ->route('customer.booking.success', ['bookingNumber' => $booking->booking_number, 'lang' => $locale])
    //             ->with('info', __('halls.already_paid'));
    //     }

    //     try {
    //         $paymentMethod = $request->input('payment_method');

    //         // Initiate payment
    //         $paymentData = $paymentService->initiatePayment($booking, $paymentMethod);

    //         if (!$paymentData['success']) {
    //             return back()->with('error', __('halls.payment_failed'));
    //         }

    //         // For online payment, redirect to gateway
    //         if ($paymentMethod === 'online' && isset($paymentData['redirect_url'])) {
    //             return redirect($paymentData['redirect_url']);
    //         }

    //         // For cash or bank transfer, go directly to success page with pending status
    //         return redirect()
    //             ->route('customer.booking.success', ['bookingNumber' => $booking->booking_number, 'lang' => $locale])
    //             ->with('success', __('halls.booking_success_pending_payment'));
    //     } catch (Exception $e) {
    //         Log::error('Payment processing failed: ' . $e->getMessage(), [
    //             'booking_id' => $booking->id,
    //             'exception' => $e
    //         ]);

    //         return back()->with('error', __('halls.payment_failed') . ' ' . $e->getMessage());
    //     }
    // }

    /**
     * Process payment
     *
     * @param Request $request
     * @param Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    // public function processPayment(Request $request, Booking $booking)
    // {
    //     // Set locale
    //     $locale = request()->get('lang', session('locale', 'ar'));
    //     app()->setLocale($locale);

    //     // Validate payment method
    //     $request->validate([
    //         'payment_method' => 'required|in:online,bank_transfer,cash',
    //     ]);

    //     $paymentService = app(PaymentService::class);

    //     // Ensure user owns this booking
    //     if ($booking->user_id !== Auth::id()) {
    //         abort(403, 'Unauthorized access to booking');
    //     }

    //     // Check if already paid
    //     if ($booking->payment_status === 'paid') {
    //         return redirect()
    //             ->route('customer.booking.success', ['bookingNumber' => $booking->booking_number, 'lang' => $locale])
    //             ->with('info', __('halls.already_paid'));
    //     }

    //     try {
    //         $paymentMethod = $request->input('payment_method');

    //         Log::info('Processing payment', [
    //             'booking_id' => $booking->id,
    //             'booking_number' => $booking->booking_number,
    //             'payment_method' => $paymentMethod,
    //             'amount' => $booking->total_amount,
    //         ]);

    //         // Initiate payment
    //         $paymentData = $paymentService->initiatePayment($booking, $paymentMethod);

    //         Log::info('Payment initiated', [
    //             'booking_id' => $booking->id,
    //             'payment_data' => $paymentData,
    //         ]);

    //         if (!$paymentData['success']) {
    //             Log::error('Payment initiation failed', [
    //                 'booking_id' => $booking->id,
    //                 'payment_data' => $paymentData,
    //             ]);
    //             return back()->with('error', __('halls.payment_failed'));
    //         }

    //         // For online payment, redirect to gateway
    //         if ($paymentMethod === 'online' && isset($paymentData['redirect_url'])) {
    //             Log::info('Redirecting to payment gateway', [
    //                 'booking_id' => $booking->id,
    //                 'redirect_url' => $paymentData['redirect_url'],
    //             ]);

    //             // Debug: Show the URL before redirect
    //             if (config('app.debug')) {
    //                 dd([
    //                     'message' => 'About to redirect to payment gateway',
    //                     'booking' => $booking->booking_number,
    //                     'payment_method' => $paymentMethod,
    //                     'redirect_url' => $paymentData['redirect_url'],
    //                     'payment_data' => $paymentData,
    //                 ]);
    //             }

    //             return redirect($paymentData['redirect_url']);
    //         }

    //         // For cash or bank transfer, go directly to success page with pending status
    //         return redirect()
    //             ->route('customer.booking.success', ['bookingNumber' => $booking->booking_number, 'lang' => $locale])
    //             ->with('success', __('halls.booking_success_pending_payment'));
    //     } catch (Exception $e) {
    //         Log::error('Payment processing exception', [
    //             'booking_id' => $booking->id,
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);

    //         return back()->with('error', __('halls.payment_failed') . ' Error: ' . $e->getMessage());
    //     }
    // }

    /**
     * Process payment
     *
     * @param Request $request
     * @param Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayment(Request $request, Booking $booking)
    {
        // Set locale
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        // Validate payment method
        $request->validate([
            'payment_method' => 'required|in:online,bank_transfer,cash',
        ]);

        $paymentService = app(PaymentService::class);

        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking');
        }

        // Check if already paid
        if ($booking->payment_status === 'paid') {
            return redirect()
                ->route('customer.booking.success', ['bookingNumber' => $booking->booking_number, 'lang' => $locale])
                ->with('info', __('halls.already_paid'));
        }

        try {
            $paymentMethod = $request->input('payment_method');

            Log::info('Processing payment', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'payment_method' => $paymentMethod,
                'amount' => $booking->total_amount,
            ]);

            // Initiate payment
            $paymentData = $paymentService->initiatePayment($booking, $paymentMethod);

            Log::info('Payment initiated', [
                'booking_id' => $booking->id,
                'payment_data' => $paymentData,
            ]);

            if (!$paymentData['success']) {
                Log::error('Payment initiation failed', [
                    'booking_id' => $booking->id,
                    'payment_data' => $paymentData,
                ]);

                // If online payment fails, offer alternative payment methods
                if ($paymentMethod === 'online') {
                    return back()->with('error', __('halls.payment_gateway_error') . ' ' . __('halls.try_alternative_payment'));
                }

                return back()->with('error', __('halls.payment_failed'));
            }

            // For online payment, redirect to gateway
            if ($paymentMethod === 'online' && isset($paymentData['redirect_url'])) {
                Log::info('Redirecting to payment gateway', [
                    'booking_id' => $booking->id,
                    'redirect_url' => $paymentData['redirect_url'],
                ]);

                return redirect($paymentData['redirect_url']);
            }

            // For cash or bank transfer, go directly to success page with pending status
            // Generate PDF even if payment is pending
            try {
                $pdfService = app(BookingPdfService::class);
                $pdfService->generateConfirmation($booking);
            } catch (Exception $e) {
                Log::warning('PDF generation failed: ' . $e->getMessage());
            }

            return redirect()
                ->route('customer.booking.success', ['bookingNumber' => $booking->booking_number, 'lang' => $locale])
                ->with('success', __('halls.booking_success_pending_payment'));
        } catch (Exception $e) {
            Log::error('Payment processing exception', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // For online payment failures, redirect to success with pending payment
            return redirect()
                ->route('customer.booking.success', ['bookingNumber' => $booking->booking_number, 'lang' => $locale])
                ->with('warning', __('halls.payment_gateway_unavailable') . ' ' . __('halls.booking_confirmed_payment_pending'));
        }
    }

    /**
     * Handle successful payment callback
     */
    public function paymentSuccess(Booking $booking)
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $paymentService = app(PaymentService::class);
            $pdfService = app(BookingPdfService::class);

            // Get the latest payment for this booking
            $payment = $booking->latestPayment;

            if ($payment && $payment->isPending() && $payment->transaction_id) {
                // Verify payment with Thawani
                $verification = $paymentService->verifyPayment($payment);

                if ($verification['success'] && $verification['is_paid']) {
                    // Process successful payment
                    $paymentService->processSuccessfulPayment($payment, $verification['data']);

                    // Generate PDF
                    $pdfService->generateConfirmation($booking);

                    return redirect()
                        ->route('customer.booking.success', ['bookingNumber' => $booking->booking_number, 'lang' => $locale])
                        ->with('success', __('halls.payment_successful'));
                }
            }

            // If payment is already processed or verification failed
            return redirect()
                ->route('customer.booking.success', ['bookingNumber' => $booking->booking_number, 'lang' => $locale])
                ->with('warning', __('halls.payment_verification_pending'));
        } catch (Exception $e) {
            Log::error('Payment success handling failed: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
            ]);

            return redirect()
                ->route('customer.booking.success', ['bookingNumber' => $booking->booking_number, 'lang' => $locale])
                ->with('warning', __('halls.payment_verification_pending'));
        }
    }

    /**
     * Handle cancelled payment
     */
    public function paymentCancel(Booking $booking)
    {
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        // Mark latest payment as cancelled
        $payment = $booking->latestPayment;
        if ($payment && $payment->isPending()) {
            $payment->update([
                'status' => Payment::STATUS_CANCELLED,
            ]);
        }

        return redirect()
            ->route('customer.booking.payment', ['booking' => $booking->id, 'lang' => $locale])
            ->with('error', __('halls.payment_cancelled'));
    }

    /**
     * Cancel booking
     *
     * @param Request $request
     * @param Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request, Booking $booking)
    {
        // Set locale
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);

        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking');
        }

        // Check if cancellation is allowed
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', __('halls.cannot_cancel'));
        }

        try {
            DB::beginTransaction();

            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->input('reason', 'Cancelled by customer'),
            ]);

            // Handle refund logic if needed
            // If paid, initiate refund through PaymentService
            if ($booking->payment_status === 'paid') {
                // $paymentService = app(PaymentService::class);
                // $paymentService->processRefund($booking);
            }

            DB::commit();

            return redirect()
                ->route('customer.bookings.index', ['lang' => $locale])
                ->with('success', __('halls.booking_cancelled'));
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Booking cancellation failed: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'exception' => $e
            ]);

            return back()->with('error', __('halls.cancellation_failed') . ' ' . $e->getMessage());
        }
    }

    /**
     * Check availability via AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hall_id' => 'required|exists:halls,id',
            'booking_date' => 'required|date|after:today',
            'time_slot' => 'required|in:morning,afternoon,evening,full_day',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'available' => false,
                'message' => __('halls.invalid_request')
            ], 422);
        }

        try {
            $bookingService = app(BookingService::class);

            $available = $bookingService->checkAvailability(
                $request->hall_id,
                $request->booking_date,
                $request->time_slot
            );

            return response()->json([
                'available' => $available,
                'message' => $available
                    ? __('halls.date_available')
                    : __('halls.date_not_available')
            ]);
        } catch (Exception $e) {
            Log::error('Availability check failed: ' . $e->getMessage());

            return response()->json([
                'available' => false,
                'message' => __('halls.checking_availability')
            ], 500);
        }
    }


    /**
     * Download booking PDF
     */
    public function downloadPdf(Booking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $pdfService = app(BookingPdfService::class);
        return $pdfService->downloadWithArabicSupport($booking);
    }
}
