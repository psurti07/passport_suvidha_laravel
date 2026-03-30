<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreDefinedMessage;
use App\Models\ApplicationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PreDefinedMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.predefmessages.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = PreDefinedMessage::with('status')->select([
                'id',
                'status_id',
                'message_name',
                'message_remarks',
                'created_at',
                'updated_at',
            ])

            ->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('status_name', function ($row) {
                return $row->status->status_name ?? '-';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i:s');
            })

            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->format('d/m/Y H:i:s');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                    
                        <!-- View -->
                        <a href="'.route('admin.predefined-messages.show', $row->id).'" 
                            class="text-blue-600 hover:text-blue-900" title="View Predefined Message">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>

                        <!-- Edit -->
                        <a href="'.route('admin.predefined-messages.edit', $row->id).'" 
                            class="text-yellow-600 hover:text-yellow-900" title="Edit Predefined Message">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </a>

                        <!-- Delete -->
                        <form action="'.route('admin.predefined-messages.destroy', $row->id).'" method="POST" class="inline">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="button" 
                                onclick="confirmDelete(\''.$row->status->status_name.' predefined message\', this.form)"
                                class="text-red-600 hover:text-red-900" 
                                title="Delete Predefined Message">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>

                    </div>
                ';
            })

            ->rawColumns(['actions'])

            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();
        return view('admin.predefmessages.create', compact('statuses')); 
        // return redirect()->route('admin.predefined-messages.index')->with('warning', 'Create form not implemented yet.'); // Placeholder removed
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'status_id' => 'required|exists:application_statuses,id',
            'message_name' => 'required|string|max:255|unique:pre_defined_messages,message_name', // Ensure name is unique
            'message_remarks' => 'required|string',
        ]);

        try {
            PreDefinedMessage::create($validatedData);

            return redirect()->route('admin.predefined-messages.index')
                             ->with('success', 'Predefined message created successfully.');
        } catch (\Exception $e) {
            // Log the error (optional but recommended)
            // Log::error('Error creating predefined message: ' . $e->getMessage());
            
            return redirect()->back()
                             ->withInput() 
                             ->with('error', 'Error creating message. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PreDefinedMessage $predefined_message)
    {
        $predefined_message->load('status');
        return view('admin.predefmessages.show', ['preDefinedMessage' => $predefined_message]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PreDefinedMessage $predefined_message)
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();
        return view('admin.predefmessages.edit', ['preDefinedMessage' => $predefined_message,'statuses' => $statuses]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PreDefinedMessage $predefined_message)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            // Ensure name is unique, ignoring the current message's ID
            'status_id' => 'required|exists:application_statuses,id',
            'message_name' => 'required|string|max:255|unique:pre_defined_messages,message_name,' . $predefined_message->id,
            'message_remarks' => 'required|string',
        ]);
        
        try {
            $predefined_message->update($validatedData);

            return redirect()->route('admin.predefined-messages.index')
                             ->with('success', 'Predefined message updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating predefined message: ' . $e->getMessage());
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Error updating message: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PreDefinedMessage $predefined_message)
    {
        try {
            $predefined_message->delete();
            return redirect()->route('admin.predefined-messages.index')->with('success', 'Message deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting message: ' . $e->getMessage());
            return redirect()->route('admin.predefined-messages.index')->with('error', 'Error deleting message.');
        }
    }
}
