<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreDefinedMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PreDefinedMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Basic implementation for index - fetch all for now
        // We can add pagination, sorting, filtering later based on the view's needs
        $query = PreDefinedMessage::query();

        // Simple search (adapt columns as needed)
        if ($search = $request->input('search')) {
            $query->where('message_name', 'like', "%{$search}%")
                  ->orWhere('message_remarks', 'like', "%{$search}%");
        }
        
        // Sorting
        $sortBy = $request->input('sort_by', 'id'); // Default sort column
        $sortDirection = $request->input('sort_direction', 'desc'); // Default sort direction

        // Validate sortable columns to prevent errors
        $sortableColumns = ['id', 'message_name', 'message_remarks', 'created_at', 'updated_at'];
        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        // Date Range Filter (assuming created_at)
        if ($fromDate = $request->input('from_date')) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate = $request->input('to_date')) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        
        // Pagination
        $perPage = $request->input('per_page', 10); // Default items per page
        $preDefinedMessages = $query->paginate($perPage);

        return view('admin.predefmessages.index', compact('preDefinedMessages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return the create view
        return view('admin.predefmessages.create'); 
        // return redirect()->route('admin.predefined-messages.index')->with('warning', 'Create form not implemented yet.'); // Placeholder removed
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'message_name' => 'required|string|max:255|unique:pre_defined_messages,message_name', // Ensure name is unique
            'message_remarks' => 'required|string',
        ]);

        try {
            // Create a new PreDefinedMessage record
            PreDefinedMessage::create($validatedData);

            // Redirect back to the index page with a success message
            return redirect()->route('admin.predefined-messages.index')
                             ->with('success', 'Predefined message created successfully.');
        } catch (\Exception $e) {
            // Log the error (optional but recommended)
            // Log::error('Error creating predefined message: ' . $e->getMessage());
            
            // Redirect back with an error message
            return redirect()->back()
                             ->withInput() // Keep old input
                             ->with('error', 'Error creating message. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PreDefinedMessage $predefined_message)
    {
        // Return the show view
        return view('admin.predefmessages.show', ['preDefinedMessage' => $predefined_message]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PreDefinedMessage $predefined_message)
    {
        // Return the edit view
        return view('admin.predefmessages.edit', ['preDefinedMessage' => $predefined_message]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PreDefinedMessage $predefined_message)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                // Ensure name is unique, ignoring the current message's ID
                'message_name' => 'required|string|max:255|unique:pre_defined_messages,message_name,' . $predefined_message->id,
                'message_remarks' => 'required|string',
            ]);

            // Update the PreDefinedMessage record
            $predefined_message->update($validatedData);

            // Redirect back to the index page with a success message
            return redirect()->route('admin.predefined-messages.index')
                             ->with('success', 'Predefined message updated successfully.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating predefined message: ' . $e->getMessage());
            
            // Redirect back with an error message
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
            // Delete the message
            $predefined_message->delete();
            
            return redirect()->route('admin.predefined-messages.index')->with('success', 'Message deleted successfully.');
        } catch (\Exception $e) {
            // Log error
            Log::error('Error deleting message: ' . $e->getMessage());
            return redirect()->route('admin.predefined-messages.index')->with('error', 'Error deleting message.');
        }
    }
    
    /**
     * Export data.
     */
    public function export(Request $request)
    {
        // Export logic needed (Excel, CSV, PDF)
        $type = $request->input('type', 'excel');
        return redirect()->route('admin.predefined-messages.index')->with('warning', "Export ({$type}) not implemented yet."); // Placeholder
    }
}
