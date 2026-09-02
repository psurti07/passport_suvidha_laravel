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
        ]);

        if ($request->filled('from_date') && $request->filled('to_date')) {

            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('status_name', function ($row) {
                return $row->status->status_name ?? '-';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y, h:i A');
            })

            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->format('d M Y, h:i A');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                    
                        <!-- View -->
                        <a href="' . route('admin.predefined-messages.show', $row->id) . '" 
                            class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:border-blue-600 transition-all duration-200 shadow-sm" title="View">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        <!-- Edit -->
                        <a href="' . route('admin.predefined-messages.edit', $row->id) . '" 
                            class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-yellow-200 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 hover:border-yellow-600 transition-all duration-200 shadow-sm" title="Edit">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>

                        <!-- Delete -->
                        <form action="' . route('admin.predefined-messages.destroy', $row->id) . '" method="POST" class="inline">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="button" 
                                onclick="confirmDelete(\'' . $row->status->status_name . ' Predefined Message\', this.form)"
                                class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 hover:border-red-600 transition-all duration-200 shadow-sm" 
                                title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>

                    </div>
                ';
            })

            ->rawColumns(['actions'])

            ->make(true);
    }

    public function create()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();
        return view('admin.predefmessages.create', compact('statuses'));
        // return redirect()->route('admin.predefined-messages.index')->with('warning', 'Create form not implemented yet.'); // Placeholder removed
    }

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


    public function show(PreDefinedMessage $predefined_message)
    {
        $predefined_message->load('status');
        return view('admin.predefmessages.show', ['preDefinedMessage' => $predefined_message]);
    }

    public function edit(PreDefinedMessage $predefined_message)
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();
        return view('admin.predefmessages.edit', ['preDefinedMessage' => $predefined_message, 'statuses' => $statuses]);
    }

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
