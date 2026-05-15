<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentLetter;
use App\Models\Customer;
use App\Models\ApplicationProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AppointmentLetterController extends Controller
{

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
                        <div class="font-semibold text-gray-900">' . ($fullName ?: '-') . '</div>
                        <div class="text-xs text-gray-500">' . $email . '</div>
                    </div>
                ';
            })

            ->addColumn('customer_mobile', function ($row) {
                return $row->customer->mobile_number ?? '-';
            })

            ->editColumn('upload_date', function ($row) {
                return $row->upload_date->format('d M Y');
            })

            ->addColumn('appointment_date_time', function ($row) {
                if ($row->appointment_date && $row->appointment_time) {

                    $date = date('d M Y', strtotime($row->appointment_date));
                    $time = date('h:i A', strtotime($row->appointment_time));

                    return $date . ', ' . $time;
                }
                return '-';
            })

            ->addColumn('user_name', function ($row) {
                return $row->uploader->name ?? 'System';
            })

            ->filterColumn('customer_name', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('mobile_number', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('user_name', function ($query, $keyword) {
                $query->whereHas('uploader', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                    
                        <!-- Preview -->
                        <a href="' . route('admin.appointment-letters.preview', $row->id) . '" 
                            class="text-blue-600 hover:text-blue-900" target="_blank" title="Preview">
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
                        <a href="' . route('admin.appointment-letters.edit', $row->id) . '" 
                            class="text-yellow-600 hover:text-yellow-900" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>

                        <!-- Download -->
                        <a href="' . route('admin.appointment-letters.download', $row->id) . '" 
                            class="text-green-600 hover:text-green-900" title="Download">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>

                        <!-- Delete -->
                        <form action="' . route('admin.appointment-letters.destroy', $row->id) . '" method="POST" class="inline">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="button" 
                                onclick="confirmDelete(\'' . $row->customer->first_name . ' Appointment Letter\', this.form)"
                                class="text-red-600 hover:text-red-900" 
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

            ->rawColumns(['customer_name', 'actions'])

            ->make(true);
    }

    public function create()
    {
        $customers = Customer::orderBy('first_name')->get();
        return view('admin.appointment-letters.create', compact('customers'));
    }

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

    public function show(AppointmentLetter $appointmentLetter)
    {
        $appointmentLetter->load(['customer', 'uploader']);
        return view('admin.appointment-letters.show', compact('appointmentLetter'));
    }

    public function edit(AppointmentLetter $appointmentLetter)
    {
        $customers = Customer::orderBy('first_name')->get();
        return view('admin.appointment-letters.edit', compact('appointmentLetter', 'customers'));
    }

    public function update(Request $request, AppointmentLetter $appointmentLetter)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'appointment_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $appointmentLetter->customer_id = $request->customer_id;
        $appointmentLetter->appointment_date = $request->appointment_date;
        $appointmentLetter->appointment_time = $request->appointment_time;

        if ($request->hasFile('appointment_letter')) {
            if (Storage::disk('public')->exists($appointmentLetter->file_path)) {
                Storage::disk('public')->delete($appointmentLetter->file_path);
            }

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

    public function destroy(AppointmentLetter $appointmentLetter)
    {
        $isUsed = ApplicationProgress::where('file_type', 'appointment_letters')
            ->where('file', $appointmentLetter->id)
            ->whereNull('deleted_at')
            ->exists();

        if ($isUsed) {
            return back()->with(
                'error',
                'Please delete the related application progress entry first.'
            );
        }

        if (Storage::disk('public')->exists($appointmentLetter->file_path)) {
            Storage::disk('public')->delete($appointmentLetter->file_path);
        }

        $appointmentLetter->delete();

        return redirect()->route('admin.appointment-letters.index')
            ->with('success', 'Appointment letter deleted successfully.');
    }

    public function download(AppointmentLetter $appointmentLetter)
    {
        if (!Storage::disk('public')->exists($appointmentLetter->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $fullPath = storage_path('app/public/' . $appointmentLetter->file_path);
        $fileName = basename($appointmentLetter->file_path);

        return response()->download($fullPath, $fileName);
    }

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
