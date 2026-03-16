<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentLetter;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AppointmentLetterController extends Controller
{
    /**
     * Display a listing of appointment letters.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = AppointmentLetter::with(['customer', 'uploader']);
        
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->whereHas('customer', function($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('mobile_number', 'like', "%{$searchTerm}%");
            });
        }
        
        // Apply date filters
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('upload_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('upload_date', '<=', $request->end_date);
        }
        
        // Sort by newest first by default
        $query->latest();
        
        $appointmentLetters = $query->paginate(10);
        
        return view('admin.appointment-letters.index', compact('appointmentLetters'));
    }

    /**
     * Show the form for creating a new appointment letter.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::orderBy('first_name')->get();
        return view('admin.appointment-letters.create', compact('customers'));
    }

    /**
     * Store a newly created appointment letter in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'appointment_letter' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $file = $request->file('appointment_letter');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('appointment_letters', $fileName, 'public');

        AppointmentLetter::create([
            'customer_id' => $request->customer_id,
            'file_path' => $filePath,
            'upload_date' => now(),
            'uploaded_by' => Auth::id(),
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
        ]);

        return redirect()->route('admin.appointment-letters.index')
            ->with('success', 'Appointment letter uploaded successfully.');
    }

    /**
     * Display the specified appointment letter.
     *
     * @param  \App\Models\AppointmentLetter  $appointmentLetter
     * @return \Illuminate\Http\Response
     */
    public function show(AppointmentLetter $appointmentLetter)
    {
        $appointmentLetter->load(['customer', 'uploader']);
        return view('admin.appointment-letters.show', compact('appointmentLetter'));
    }

    /**
     * Show the form for editing the specified appointment letter.
     *
     * @param  \App\Models\AppointmentLetter  $appointmentLetter
     * @return \Illuminate\Http\Response
     */
    public function edit(AppointmentLetter $appointmentLetter)
    {
        $customers = Customer::orderBy('first_name')->get();
        return view('admin.appointment-letters.edit', compact('appointmentLetter', 'customers'));
    }

    /**
     * Update the specified appointment letter in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AppointmentLetter  $appointmentLetter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AppointmentLetter $appointmentLetter)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'appointment_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        // Update customer ID
        $appointmentLetter->customer_id = $request->customer_id;
        $appointmentLetter->appointment_date = $request->appointment_date;
        $appointmentLetter->appointment_time = $request->appointment_time;

        // Update file if provided
        if ($request->hasFile('appointment_letter')) {
            // Delete old file
            if (Storage::disk('public')->exists($appointmentLetter->file_path)) {
                Storage::disk('public')->delete($appointmentLetter->file_path);
            }

            // Upload new file
            $file = $request->file('appointment_letter');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('appointment_letters', $fileName, 'public');

            $appointmentLetter->file_path = $filePath;
            $appointmentLetter->upload_date = now();
            $appointmentLetter->uploaded_by = Auth::id();
        }

        $appointmentLetter->save();

        return redirect()->route('admin.appointment-letters.index')
            ->with('success', 'Appointment letter updated successfully.');
    }

    /**
     * Remove the specified appointment letter from storage.
     *
     * @param  \App\Models\AppointmentLetter  $appointmentLetter
     * @return \Illuminate\Http\Response
     */
    public function destroy(AppointmentLetter $appointmentLetter)
    {
        // Delete the associated file
        if (Storage::disk('public')->exists($appointmentLetter->file_path)) {
            Storage::disk('public')->delete($appointmentLetter->file_path);
        }

        // Delete the record
        $appointmentLetter->delete();

        return redirect()->route('admin.appointment-letters.index')
            ->with('success', 'Appointment letter deleted successfully.');
    }

    /**
     * Download the appointment letter file.
     *
     * @param  \App\Models\AppointmentLetter  $appointmentLetter
     * @return \Illuminate\Http\Response
     */
    public function download(AppointmentLetter $appointmentLetter)
    {
        if (!Storage::disk('public')->exists($appointmentLetter->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }
        
        $fullPath = storage_path('app/public/' . $appointmentLetter->file_path);
        $fileName = basename($appointmentLetter->file_path);
        
        return response()->download($fullPath, $fileName);
    }
    
    /**
     * Preview the appointment letter file.
     *
     * @param  \App\Models\AppointmentLetter  $appointmentLetter
     * @return \Illuminate\Http\Response
     */
    public function preview(AppointmentLetter $appointmentLetter)
    {
        if (!Storage::disk('public')->exists($appointmentLetter->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }
        
        $fullPath = storage_path('app/public/' . $appointmentLetter->file_path);
        $contentType = mime_content_type($fullPath);
        
        return response()->file($fullPath, ['Content-Type' => $contentType]);
    }
}
