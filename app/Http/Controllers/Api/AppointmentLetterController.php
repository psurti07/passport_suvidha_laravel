<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppointmentLetter;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AppointmentLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check if customer_id is provided to filter by customer
        if ($request->has('customer_id')) {
            $customer = Customer::findOrFail($request->customer_id);
            $appointmentLetters = $customer->appointmentLetters()
                ->with('uploader:id,name')
                ->latest()
                ->paginate(10);
        } else {
            // Get all appointment letters if no customer_id is provided
            $appointmentLetters = AppointmentLetter::with(['customer:id,first_name,last_name', 'uploader:id,name'])
                ->latest()
                ->paginate(10);
        }

        return response()->json([
            'success' => true,
            'data' => $appointmentLetters
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'appointment_letter' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Upload file
        $file = $request->file('appointment_letter');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('appointment_letters', $fileName, 'public');

        // Create appointment letter record
        $appointmentLetter = AppointmentLetter::create([
            'customer_id' => $request->customer_id,
            'file_path' => $filePath,
            'upload_date' => now(),
            'uploaded_by' => auth()->id(),
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment letter uploaded successfully',
            'data' => $appointmentLetter
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $appointmentLetter = AppointmentLetter::with(['customer:id,first_name,last_name', 'uploader:id,name'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $appointmentLetter
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Find the appointment letter
        $appointmentLetter = AppointmentLetter::findOrFail($id);

        // Validate request data
        $validator = Validator::make($request->all(), [
            'customer_id' => 'sometimes|required|exists:customers,id',
            'appointment_letter' => 'sometimes|required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'appointment_date' => 'sometimes|required|date',
            'appointment_time' => 'sometimes|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update customer ID if provided
        if ($request->has('customer_id')) {
            $appointmentLetter->customer_id = $request->customer_id;
        }

        // Update appointment date if provided
        if ($request->has('appointment_date')) {
            $appointmentLetter->appointment_date = $request->appointment_date;
        }

        // Update appointment time if provided
        if ($request->has('appointment_time')) {
            $appointmentLetter->appointment_time = $request->appointment_time;
        }

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
            $appointmentLetter->uploaded_by = auth()->id();
        }

        $appointmentLetter->save();

        return response()->json([
            'success' => true,
            'message' => 'Appointment letter updated successfully',
            'data' => $appointmentLetter
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $appointmentLetter = AppointmentLetter::findOrFail($id);

        // Delete the associated file
        if (Storage::disk('public')->exists($appointmentLetter->file_path)) {
            Storage::disk('public')->delete($appointmentLetter->file_path);
        }

        // Delete the record
        $appointmentLetter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appointment letter deleted successfully'
        ]);
    }

    /**
     * List all appointment letters for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function listUserLetters()
    {
        $customer = auth()->user();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $appointmentLetters = AppointmentLetter::where('customer_id', $customer->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $appointmentLetters
        ]);
    }

    /**
     * Download a specific appointment letter by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadById($id)
    {
        $customer = auth()->user();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $appointmentLetter = AppointmentLetter::where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$appointmentLetter) {
            return response()->json([
                'success' => false,
                'message' => 'No appointment letter found or you do not have access to this letter'
            ], Response::HTTP_NOT_FOUND);
        }

        if (!Storage::disk('public')->exists($appointmentLetter->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $fullPath = storage_path('app/public/' . $appointmentLetter->file_path);
        $fileName = basename($appointmentLetter->file_path);

        return response()->download($fullPath, $fileName);
    }

    /**
     * Download the appointment letter file.
     *
     * @return \Illuminate\Http\Response
     */
    public function download()
    {
        $customer = auth()->user();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $appointmentLetter = AppointmentLetter::where('customer_id', $customer->id)
            ->latest()
            ->first();

        if (!$appointmentLetter) {
            return response()->json([
                'success' => false,
                'message' => 'No appointment letter found'
            ], Response::HTTP_NOT_FOUND);
        }

        if (!Storage::disk('public')->exists($appointmentLetter->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $fullPath = storage_path('app/public/' . $appointmentLetter->file_path);
        $fileName = basename($appointmentLetter->file_path);

        return response()->download($fullPath, $fileName);
    }
}
