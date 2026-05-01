<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketRemark;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    /**
     * Display the customer support page with tickets from registered users.
     *
     * @return \Illuminate\View\View
     */
    public function customerSupport()
    {
        return view('admin.support.customer_support');
    }

    public function customerSupportData(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = Ticket::with('customer')->select([
            'id',
            'ticket_number',
            'customer_id',
            'name',
            'email',
            'subject',
            'status',
            'created_at',
        ])
        ->whereNotNull('customer_id') // Only tickets from registered customers
        ->whereBetween('created_at', [
            $from . ' 00:00:00',
            $to . ' 23:59:59'
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('subject', function ($row) {
                return Str::limit($row->subject, 50);
            })
            
            ->editColumn('status', function ($row) {
                if ($row->status == 'open') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">Open</span>';
                } elseif ($row->status == 'in_progress') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">In Progress</span>';
                } else {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-red-100 text-red-800">Closed</span>';
                }
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y, h:i A');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                    
                        <!-- View -->
                        <a href="'.route('admin.support.tickets.show', $row->ticket_number).'" 
                            class="text-blue-600 hover:text-blue-900" title="View">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>

                    </div>
                ';
            })

            ->rawColumns(['status', 'actions'])

            ->make(true);
    }

    /**
     * Display the guest user support page with tickets from guests.
     *
     * @return \Illuminate\View\View
     */
    // public function guestSupport()
    // {
    //     // Fetch tickets created by guests (customer_id is null)
    //     $tickets = Ticket::whereNull('customer_id')->latest()->paginate(15); // Paginate results
        
    //     return view('admin.support.guest_support', compact('tickets'));
    // }
    public function guestSupport()
    {
        return view('admin.support.guest_support');
    }

    public function guestSupportData(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = Ticket::with('customer')->select([
            'id',
            'ticket_number',
            'customer_id',
            'name',
            'email',
            'subject',
            'status',
            'created_at',
        ])
        ->whereNull('customer_id') // Only tickets from guests
        ->whereBetween('created_at', [
            $from . ' 00:00:00',
            $to . ' 23:59:59'
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('subject', function ($row) {
                return Str::limit($row->subject, 50);
            })
            
            ->editColumn('status', function ($row) {
                if ($row->status == 'open') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">Open</span>';
                } elseif ($row->status == 'in_progress') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">In Progress</span>';
                } else {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-red-100 text-red-800">Closed</span>';
                }
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y, h:i A');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                    
                        <!-- View -->
                        <a href="'.route('admin.support.tickets.show', $row->ticket_number).'" 
                            class="text-blue-600 hover:text-blue-900" title="View">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>

                    </div>
                ';
            })

            ->rawColumns(['status', 'actions'])

            ->make(true);
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
        $ticket->load(['ticketRemarks.user',
            'customer.order'
        ]);

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