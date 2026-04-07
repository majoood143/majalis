<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Mail\TicketSubmittedAdminMail;
use App\Mail\TicketSubmittedCustomerMail;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketMessageType;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Exception;

/**
 * Guest Ticket Controller
 *
 * Handles two-step ticket submission for non-authenticated customers.
 *
 * Step 1 – Verify:  Guest provides booking_number, email, or phone.
 *                   We locate the booking and store a session key so
 *                   they cannot skip straight to the form.
 *
 * Step 2 – Submit:  Guest fills in ticket details.  The ticket is
 *                   created with the booking's user_id when available,
 *                   or with user_id = null for pure guest bookings.
 *                   The guest's email is stored in metadata so tickets
 *                   are automatically linked when they register.
 */
class GuestTicketController extends Controller
{
    /** Session key used to pass verified booking between steps. */
    private const SESSION_KEY = 'guest_ticket_booking_id';

    // -------------------------------------------------------------------------
    // STEP 1 – VERIFY BOOKING
    // -------------------------------------------------------------------------

    /**
     * Show the booking-verification form (step 1).
     */
    public function verify()
    {
        // If already logged in, redirect to the authenticated create form
        if (auth()->check()) {
            return redirect()->route('customer.tickets.create');
        }

        return view('customer.tickets.guest.verify');
    }

    /**
     * Validate the booking lookup and proceed to step 2.
     */
    public function lookup(Request $request)
    {
        if (auth()->check()) {
            return redirect()->route('customer.tickets.create');
        }

        $validator = Validator::make($request->all(), [
            'lookup' => 'required|string|max:255',
        ], [
            'lookup.required' => __('tickets_guest.lookup_required'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $value = trim($request->input('lookup'));

        // Search bookings by booking_number, customer_email, or customer_phone
        $bookings = Booking::with('hall')
            ->where('booking_number', $value)
            ->orWhere('customer_email', strtolower($value))
            ->orWhere('customer_phone', $value)
            ->latest()
            ->get();

        if ($bookings->isEmpty()) {
            return back()
                ->withErrors(['lookup' => __('tickets_guest.booking_not_found')])
                ->withInput();
        }

        // Single match — skip the selection step
        if ($bookings->count() === 1) {
            $request->session()->put(self::SESSION_KEY, $bookings->first()->id);
            return redirect()->route('guest.tickets.create');
        }

        // Multiple matches — let the customer choose
        return view('customer.tickets.guest.select', compact('bookings'));
    }

    // -------------------------------------------------------------------------
    // STEP 1b – SELECT BOOKING (when multiple matches found)
    // -------------------------------------------------------------------------

    /**
     * Handle the booking selection form and store the chosen booking in session.
     */
    public function selectBooking(Request $request)
    {
        if (auth()->check()) {
            return redirect()->route('customer.tickets.create');
        }

        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer|exists:bookings,id',
        ], [
            'booking_id.required' => __('tickets_guest.select_required'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $request->session()->put(self::SESSION_KEY, (int) $request->input('booking_id'));

        return redirect()->route('guest.tickets.create');
    }

    // -------------------------------------------------------------------------
    // STEP 2 – SUBMIT TICKET
    // -------------------------------------------------------------------------

    /**
     * Show the ticket submission form (step 2).
     */
    public function create(Request $request)
    {
        if (auth()->check()) {
            return redirect()->route('customer.tickets.create');
        }

        $bookingId = $request->session()->get(self::SESSION_KEY);

        if (! $bookingId) {
            return redirect()->route('guest.tickets.verify')
                ->with('error', __('tickets_guest.verify_first'));
        }

        $booking = Booking::find($bookingId);

        if (! $booking) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('guest.tickets.verify')
                ->with('error', __('tickets_guest.booking_not_found'));
        }

        $ticketTypes = TicketType::cases();

        return view('customer.tickets.guest.create', compact('booking', 'ticketTypes'));
    }

    /**
     * Store the guest ticket.
     */
    public function store(Request $request)
    {
        if (auth()->check()) {
            return redirect()->route('customer.tickets.create');
        }

        $bookingId = $request->session()->get(self::SESSION_KEY);

        if (! $bookingId) {
            return redirect()->route('guest.tickets.verify')
                ->with('error', __('tickets_guest.verify_first'));
        }

        $booking = Booking::find($bookingId);

        if (! $booking) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('guest.tickets.verify')
                ->with('error', __('tickets_guest.booking_not_found'));
        }

        $validator = Validator::make($request->all(), [
            'type'        => 'required|in:' . implode(',', array_column(TicketType::cases(), 'value')),
            'subject'     => 'required|string|max:200',
            'description' => 'required|string|min:10',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,csv',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $ticket = Ticket::create([
                'user_id'    => $booking->user_id, // null for pure guest bookings
                'booking_id' => $booking->id,
                'type'       => $request->type,
                'priority'   => TicketPriority::MEDIUM->value,
                'status'     => TicketStatus::OPEN->value,
                'subject'    => $request->subject,
                'description' => $request->description,
                'metadata'   => [
                    'guest_email' => strtolower($booking->customer_email),
                    'guest_name'  => $booking->customer_name,
                    'guest_phone' => $booking->customer_phone,
                    'submitted_as_guest' => true,
                ],
            ]);

            // Handle attachments
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('ticket-attachments', 'private');
                    $attachments[] = [
                        'path'          => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type'     => $file->getMimeType(),
                        'size'          => $file->getSize(),
                        'uploaded_at'   => now()->toIso8601String(),
                    ];
                }
            }

            $ticket->addMessage(
                $request->description,
                // Use booking user_id for message author when available,
                // otherwise fall back to the first admin (id=1) as a placeholder
                // so the foreign-key constraint on ticket_messages is satisfied.
                $booking->user_id ?? 1,
                TicketMessageType::CUSTOMER_REPLY,
                $attachments
            );

            DB::commit();

            // Send confirmation email to guest
            Mail::to($booking->customer_email)->send(new TicketSubmittedCustomerMail(
                $ticket,
                $booking->customer_name,
                $booking->customer_email
            ));

            // Send notification email to admin staff
            $adminUsers = User::where('role', UserRole::ADMIN)->where('is_active', true)->get();
            foreach ($adminUsers as $admin) {
                Mail::to($admin->email)->send(new TicketSubmittedAdminMail($ticket));
            }

            // Clear session after successful submission
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->route('guest.tickets.success')
                ->with('ticket_number', $ticket->ticket_number)
                ->with('guest_email', $booking->customer_email);
        } catch (Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', __('tickets_guest.submit_failed') . ': ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the success / confirmation page.
     */
    public function success(Request $request)
    {
        // Require that we arrived here via a redirect with ticket_number in flash
        if (! $request->session()->has('ticket_number')) {
            return redirect()->route('guest.tickets.verify');
        }

        return view('customer.tickets.guest.success', [
            'ticketNumber' => $request->session()->get('ticket_number'),
            'guestEmail'   => $request->session()->get('guest_email'),
        ]);
    }
}
