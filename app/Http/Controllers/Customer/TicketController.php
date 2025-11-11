<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketMessageType;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Exception;

/**
 * Customer Ticket Controller
 * 
 * Handles customer-facing ticket operations including creating new tickets,
 * viewing ticket details, adding replies, and managing attachments.
 * 
 * @package App\Http\Controllers\Customer
 * @version 1.0.0
 */
class TicketController extends Controller
{
    /**
     * Display a listing of customer's tickets.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $status = $request->input('status');
        $type = $request->input('type');

        // Build query
        $query = Ticket::where('user_id', Auth::id())
            ->with(['messages' => function($q) {
                $q->where('is_internal', false)
                  ->latest()
                  ->take(1);
            }])
            ->latest();

        // Apply filters
        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('type', $type);
        }

        // Paginate results
        $tickets = $query->paginate(15);

        // Get filter options
        $statuses = TicketStatus::toSelectArray();
        $types = TicketType::toSelectArray();

        return view('customer.tickets.index', compact(
            'tickets',
            'statuses',
            'types',
            'status',
            'type'
        ));
    }

    /**
     * Show the form for creating a new ticket.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get customer's bookings for linking
        $bookings = Booking::where('user_id', Auth::id())
            ->with('hall')
            ->latest()
            ->get();

        // Get ticket types and priorities
        $ticketTypes = TicketType::cases();
        $priorities = TicketPriority::cases();

        return view('customer.tickets.create', compact(
            'bookings',
            'ticketTypes',
            'priorities'
        ));
    }

    /**
     * Store a newly created ticket.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:' . implode(',', array_column(TicketType::cases(), 'value')),
            'priority' => 'nullable|in:' . implode(',', array_column(TicketPriority::cases(), 'value')),
            'subject' => 'required|string|max:200',
            'description' => 'required|string|min:10',
            'booking_id' => 'nullable|exists:bookings,id',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,csv',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create the ticket
            $ticket = Ticket::create([
                'user_id' => Auth::id(),
                'type' => $request->type,
                'priority' => $request->priority ?? TicketPriority::MEDIUM->value,
                'status' => TicketStatus::OPEN->value,
                'subject' => $request->subject,
                'description' => $request->description,
                'booking_id' => $request->booking_id,
            ]);

            // Handle file attachments if any
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('ticket-attachments', 'private');
                    
                    $attachments[] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'uploaded_at' => now()->toIso8601String(),
                    ];
                }
            }

            // Create initial message with attachments
            $ticket->addMessage(
                $request->description,
                Auth::id(),
                TicketMessageType::CUSTOMER_REPLY,
                $attachments
            );

            DB::commit();

            // Send notification to admin/staff (implement as needed)
            // $this->notifyStaffAboutNewTicket($ticket);

            return redirect()
                ->route('customer.tickets.show', $ticket)
                ->with('success', 'Ticket created successfully! Ticket #' . $ticket->ticket_number);

        } catch (Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Failed to create ticket: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified ticket.
     * 
     * @param Ticket $ticket
     * @return \Illuminate\View\View
     */
    public function show(Ticket $ticket)
    {
        // Ensure customer owns this ticket
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        // Load relationships
        $ticket->load([
            'booking.hall',
            'messages' => function($q) {
                // Only show customer-visible messages
                $q->where('is_internal', false)
                  ->with('user')
                  ->orderBy('created_at', 'asc');
            }
        ]);

        // Mark unread staff replies as read
        $ticket->messages()
            ->where('type', TicketMessageType::STAFF_REPLY->value)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('customer.tickets.show', compact('ticket'));
    }

    /**
     * Add a reply to the ticket.
     * 
     * @param Request $request
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reply(Request $request, Ticket $ticket)
    {
        // Ensure customer owns this ticket
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if ticket is closed
        if ($ticket->status === TicketStatus::CLOSED) {
            return back()->with('error', 'Cannot reply to a closed ticket. Please create a new ticket.');
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:10',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,csv',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Handle file attachments
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('ticket-attachments', 'private');
                    
                    $attachments[] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'uploaded_at' => now()->toIso8601String(),
                    ];
                }
            }

            // Add the reply
            $ticket->addMessage(
                $request->message,
                Auth::id(),
                TicketMessageType::CUSTOMER_REPLY,
                $attachments
            );

            // Update ticket status to pending if it was resolved
            if ($ticket->status === TicketStatus::RESOLVED) {
                $ticket->update(['status' => TicketStatus::PENDING]);
            }

            DB::commit();

            // Notify staff about new reply (implement as needed)
            // $this->notifyStaffAboutReply($ticket);

            return back()->with('success', 'Reply added successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to add reply: ' . $e->getMessage());
        }
    }

    /**
     * Download an attachment.
     * 
     * @param Ticket $ticket
     * @param int $messageId
     * @param int $index
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadAttachment(Ticket $ticket, int $messageId, int $index)
    {
        // Ensure customer owns this ticket
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        // Find the message
        $message = $ticket->messages()->findOrFail($messageId);

        // Check if attachment exists
        if (!$message->has_attachments || !isset($message->attachments[$index])) {
            abort(404, 'Attachment not found.');
        }

        $attachment = $message->attachments[$index];

        // Check if file exists in storage
        if (!Storage::disk('private')->exists($attachment['path'])) {
            abort(404, 'File not found in storage.');
        }

        // Download the file
        return Storage::disk('private')->download(
            $attachment['path'],
            $attachment['original_name'] ?? basename($attachment['path'])
        );
    }

    /**
     * Close a ticket (customer request).
     * 
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function close(Ticket $ticket)
    {
        // Ensure customer owns this ticket
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if ticket can be closed
        if (!in_array($ticket->status, [TicketStatus::RESOLVED, TicketStatus::OPEN])) {
            return back()->with('error', 'This ticket cannot be closed at the moment.');
        }

        try {
            $ticket->close();

            return back()->with('success', 'Ticket closed successfully!');

        } catch (Exception $e) {
            return back()->with('error', 'Failed to close ticket: ' . $e->getMessage());
        }
    }

    /**
     * Rate a closed ticket.
     * 
     * @param Request $request
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rate(Request $request, Ticket $ticket)
    {
        // Ensure customer owns this ticket
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if ticket is closed
        if ($ticket->status !== TicketStatus::CLOSED) {
            return back()->with('error', 'You can only rate closed tickets.');
        }

        // Check if already rated
        if ($ticket->rating) {
            return back()->with('error', 'You have already rated this ticket.');
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $ticket->rate($request->rating, $request->feedback);

            return back()->with('success', 'Thank you for your feedback!');

        } catch (Exception $e) {
            return back()->with('error', 'Failed to submit rating: ' . $e->getMessage());
        }
    }

    /**
     * Reopen a closed ticket.
     * 
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reopen(Ticket $ticket)
    {
        // Ensure customer owns this ticket
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if ticket can be reopened
        if (!$ticket->canBeReopened()) {
            return back()->with('error', 'This ticket cannot be reopened.');
        }

        try {
            $ticket->update(['status' => TicketStatus::OPEN]);

            // Add system message
            $ticket->addMessage(
                'Ticket reopened by customer.',
                Auth::id(),
                TicketMessageType::SYSTEM_MESSAGE
            );

            return back()->with('success', 'Ticket has been reopened.');

        } catch (Exception $e) {
            return back()->with('error', 'Failed to reopen ticket: ' . $e->getMessage());
        }
    }
}
