<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentLetter;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AppointmentLetterController extends Controller
{
    /**
     * Display a listing of appointment letters.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index(Request $request)
    // {
    //     $query = AppointmentLetter::with(['customer', 'uploader']);
        
    //     // Apply search filter
    //     if ($request->has('search') && !empty($request->search)) {
    //         $searchTerm = $request->search;
    //         $query->whereHas('customer', function($q) use ($searchTerm) {
    //             $q->where('first_name', 'like', "%{$searchTerm}%")
    //               ->orWhere('last_name', 'like', "%{$searchTerm}%")
    //               ->orWhere('email', 'like', "%{$searchTerm}%")
    //               ->orWhere('mobile_number', 'like', "%{$searchTerm}%");
    //         });
    //     }
        
    //     // Apply date filters
    //     if ($request->has('start_date') && !empty($request->start_date)) {
    //         $query->whereDate('upload_date', '>=', $request->start_date);
    //     }
        
    //     if ($request->has('end_date') && !empty($request->end_date)) {
    //         $query->whereDate('upload_date', '<=', $request->end_date);
    //     }
        
    //     // Sort by newest first by default
    //     $query->latest();
        
    //     $appointmentLetters = $query->paginate(10);
        
    //     return view('admin.appointment-letters.index-old', compact('appointmentLetters'));
    // }
    public function index()
    {
        return view('admin.appointment-letters.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = AppointmentLetter::with('customer', 'uploader')->select([
                'appointment_letters.id',
                'appointment_letters.customer_id',
                'appointment_letters.upload_date',
                'appointment_letters.appointment_date',
                'appointment_letters.appointment_time',
                'appointment_letters.uploaded_by',
            ])

            ->whereBetween('appointment_letters.upload_date', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('customer_name', function ($row) {
                $firstName = $row->customer->first_name ?? '';
                $lastName  = $row->customer->last_name ?? '';
                $email     = $row->customer->email ?? '';

                $fullName = trim($firstName . ' ' . $lastName);

                return '
                    <div>
                        <div class="font-semibold text-gray-900">'.($fullName ?: '-').'</div>
                        <div class="text-xs text-gray-500">'.$email.'</div>
                    </div>
                ';
            })

            ->addColumn('mobile', function ($row) {
                return $row->customer->mobile_number ?? '-';
            })

            ->editColumn('upload_date', function ($row) {
                return $row->upload_date->format('d/m/Y');
            })

            ->addColumn('appointment_date_time', function ($row) {
                if ($row->appointment_date && $row->appointment_time) {

                    $date = date('d/m/Y', strtotime($row->appointment_date));
                    $time = date('h:i A', strtotime($row->appointment_time));

                    return $date . ' at ' . $time;
                }
                return '-';
            })

            ->addColumn('name', function ($row) {
                return $row->uploader->name ?? 'System';
            })     

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                    
                        <!-- View -->
                        <a href="'.route('admin.appointment-letters.preview', $row->id).'" 
                            class="text-blue-600 hover:text-blue-900" target="_blank" title="View Appointment Letter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>

                        <!-- Edit -->
                        <a href="'.route('admin.appointment-letters.edit', $row->id).'" 
                            class="text-yellow-600 hover:text-yellow-900" title="Edit Appointment Letter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </a>

                        <!-- Download -->
                        <a href="'.route('admin.appointment-letters.download', $row->id).'" 
                            class="text-green-600 hover:text-green-900" title="Download Appointment Letter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>

                        <!-- Delete -->
                        <form action="'.route('admin.appointment-letters.destroy', $row->id).'" method="POST" class="inline">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="button" 
                                onclick="confirmDelete(\''.$row->customer->first_name.' Appointment Letter\', this.form)"
                                class="text-red-600 hover:text-red-900" 
                                title="Delete Appointment Letter">
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

            ->rawColumns(['customer_name', 'actions'])

            ->make(true);
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
