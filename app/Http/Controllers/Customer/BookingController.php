<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hall;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected PaymentService $paymentService
    ) {
        $this->middleware('auth');
    }

    /**
     * Store a new booking
     */
    public function store(Request $request, Hall $hall)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'booking_date' => 'required|date|after:today',
            'time_slot' => 'required|in:morning,afternoon,evening,full_day',
            'number_of_guests' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_notes' => 'nullable|string|max:1000',
            'event_type' => 'nullable|string|max:100',
            'event_details' => 'nullable|string|max:1000',
            'extra_services' => 'nullable|array',
            'extra_services.*.service_id' => 'required|exists:extra_services,id',
            'extra_services.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Check availability
            if (!$this->bookingService->checkAvailability(
                $hall->id,
                $request->booking_date,
                $request->time_slot
            )) {
                return back()
                    ->with('error', 'The selected time slot is not available. Please choose another date or time.')
                    ->withInput();
            }

            // Validate guest count
            if ($request->number_of_guests < $hall->capacity_min || 
                $request->number_of_guests > $hall->capacity_max) {
                return back()
                    ->with('error', "Number of guests must be between {$hall->capacity_min} and {$hall->capacity_max}.")
                    ->withInput();
            }

            // Create booking
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
                'extra_services' => $request->extra_services ?? [],
            ];

            $booking = $this->bookingService->createBooking($bookingData);

            // Redirect to payment if amount > 0
            if ($booking->total_amount > 0) {
                return redirect()
                    ->route('customer.booking.payment', $booking)
                    ->with('success', 'Booking created successfully! Please proceed with payment.');
            }

            return redirect()
                ->route('customer.booking.details', $booking)
                ->with('success', 'Booking created successfully!');

        } catch (Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show payment page
     */
    public function payment(Booking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if already paid
        if ($booking->payment_status === 'paid') {
            return redirect()
                ->route('customer.booking.details', $booking)
                ->with('info', 'This booking has already been paid.');
        }

        $booking->load('hall.city');

        return view('customer.payment', compact('booking'));
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, Booking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if already paid
        if ($booking->payment_status === 'paid') {
            return redirect()
                ->route('customer.booking.details', $booking)
                ->with('info', 'This booking has already been paid.');
        }

        try {
            $paymentData = $this->paymentService->initiatePayment($booking);

            if (isset($paymentData['redirect_url'])) {
                return redirect($paymentData['redirect_url']);
            }

            return back()->with('error', 'Failed to initiate payment. Please try again.');

        } catch (Exception $e) {
            return back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Cancel booking
     */
    public function cancel(Request $request, Booking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if cancellation is allowed
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        try {
            DB::beginTransaction();

            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->input('reason', 'Cancelled by customer'),
            ]);

            // Handle refund logic if needed
            // ...

            DB::commit();

            return redirect()
                ->route('customer.bookings')
                ->with('success', 'Booking cancelled successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel booking: ' . $e->getMessage());
        }
    }

    /**
     * Check availability via AJAX
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hall_id' => 'required|exists:halls,id',
            'booking_date' => 'required|date',
            'time_slot' => 'required|in:morning,afternoon,evening,full_day',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'available' => false,
                'message' => 'Invalid input'
            ], 422);
        }

        $available = $this->bookingService->checkAvailability(
            $request->hall_id,
            $request->booking_date,
            $request->time_slot
        );

        return response()->json([
            'available' => $available,
            'message' => $available 
                ? 'This time slot is available!' 
                : 'This time slot is not available. Please select another date or time.'
        ]);
    }
}
