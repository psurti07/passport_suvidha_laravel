<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketRemark;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /**
     * Display the customer support page with tickets from registered users.
     *
     * @return \Illuminate\View\View
     */
    public function customerSupport()
    {
        // Fetch tickets created by registered users (customer_id is not null)
        $tickets = Ticket::whereNotNull('customer_id')->latest()->paginate(15); // Paginate results
        
        return view('admin.support.customer_support', compact('tickets'));
    }

    /**
     * Display the guest user support page with tickets from guests.
     *
     * @return \Illuminate\View\View
     */
    public function guestSupport()
    {
        // Fetch tickets created by guests (customer_id is null)
        $tickets = Ticket::whereNull('customer_id')->latest()->paginate(15); // Paginate results
        
        return view('admin.support.guest_support', compact('tickets'));
    }

    /**
     * Display the specified ticket.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\View\View
     */
    public function show(Ticket $ticket)
    {
        // Eager load remarks and their associated user (staff)
        $ticket->load(['ticketRemarks.user']);

        // Determine user type for display
        $userType = $ticket->customer_id ? 'Customer' : 'Guest';

        // Eager load the customer if it exists
        if ($ticket->customer_id) {
            $ticket->load('customer'); // Ensure customer is loaded for card_no etc.
        }

        return view('admin.support.show', compact('ticket', 'userType'));
    }

    /**
     * Store a new remark for the specified ticket.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeRemark(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'remark' => 'required|string|min:5',
        ]);

        // Use the renamed TicketRemark model
        $remark = new TicketRemark();
        $remark->comment = $validated['remark'];
        $remark->user_id = Auth::id(); // Associate with the logged-in admin/staff
        $remark->ticket_number = $ticket->ticket_number;
        $remark->save();

        // Optionally, update ticket status if needed, e.g., to 'in_progress'
        if ($ticket->status === 'open') {
             $ticket->status = 'in_progress';
             $ticket->save();
        }

        // Load the user relationship for the response
        $remark->load('user');

        // Format the created_at date
        $remark->created_at_formatted = $remark->created_at->format('d/m/Y H:i');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Remark added successfully.',
                'remark' => [
                    'comment' => $remark->comment,
                    'created_at' => $remark->created_at_formatted,
                ],
                'user' => [
                    'name' => $remark->user->name
                ]
            ]);
        }

        return redirect()->route('admin.support.tickets.show', $ticket->ticket_number)
                         ->with('success', 'Remark added successfully.');
    }

    /**
     * Update the status of the specified ticket.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:open,in_progress,closed', // Validate allowed statuses
        ]);

        $ticket->status = $validated['status'];
        $ticket->save();

        // Optional: Add an automatic remark when status changes
        // Use the renamed TicketRemark model
        $remark = new TicketRemark();
        $remark->comment = "Ticket status updated to '" . ucfirst(str_replace('_', ' ', $validated['status'])) . "'.";
        $remark->user_id = Auth::id(); // Action performed by logged-in admin
        $remark->ticket_number = $ticket->ticket_number;
        $remark->save();

        return redirect()->route('admin.support.tickets.show', $ticket->ticket_number)
                         ->with('success', 'Ticket status updated successfully.');
    }

    public function showCustomerTickets()
    {
        // This seems redundant with customerSupport(), consider removing or clarifying purpose
        $tickets = Ticket::whereNotNull('customer_id')->latest()->paginate(15);

        return view('admin.support.customer', compact('tickets'));
    }
} 