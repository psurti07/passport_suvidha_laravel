<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationProgress;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\FinalDetail;
use App\Models\AppointmentLetter;
use App\Models\ApplicationStatus;

class ApplicationProgressController extends Controller
{
    /**
     * Display a listing of application progress entries.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = ApplicationProgress::with(['customer', 'remarkedByUser']);
        
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        if ($request->has('status_id')) {
            $query->where('status_id', $request->status_id);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }
        
        $applicationProgress = $query->latest()->paginate(15);
        return view('admin.application-progress.index', compact('applicationProgress'));
    }

    /**
     * Show the form for creating a new application progress entry.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::all();
        return view('admin.application-progress.create', compact('customers'));
    }

    /**
     * Store a newly created application progress entry in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(Request $request)
    {
        $status = \App\Models\ApplicationStatus::find($request->status_id);
        $slug = $status?->slug;

        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'status_id' => 'required|exists:application_statuses,id',
            'status_date' => 'required|date',
            'remark' => 'required|string',
            'redirect' => 'nullable|string',
        ];

        if (in_array($slug, [
            'details_verification',
            'appointment_scheduled',
            'appointment_rescheduled1',
            'appointment_rescheduled2',
            'appointment_rescheduled3'
        ])) {
            $rules['file'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        if (in_array($slug, [
            'appointment_scheduled',
            'appointment_rescheduled1',
            'appointment_rescheduled2',
            'appointment_rescheduled3'
        ])) {
            $rules['appointment_date'] = 'required|date';
            $rules['appointment_time'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect($request->redirect ?? url()->previous())
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['remarked_by'] = Auth::id();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            if ($slug === 'details_verification') {
                $finalDetail = FinalDetail::create([
                    'customer_id' => $request->customer_id,
                    'file_path' => $filePath,
                    'upload_date' => now(),
                    'uploaded_by' => Auth::id(),
                    'is_approved' => false
                ]);

                $data['file_type'] = 'final_details';
                $data['file'] = $finalDetail->id;

            } elseif (in_array($slug, [
                'appointment_scheduled',
                'appointment_rescheduled1',
                'appointment_rescheduled2',
                'appointment_rescheduled3'
            ])) {

                $appointmentLetter = AppointmentLetter::create([
                    'customer_id' => $request->customer_id,
                    'file_path' => $filePath,
                    'upload_date' => now(),
                    'uploaded_by' => Auth::id(),
                    'appointment_date' => $request->appointment_date,
                    'appointment_time' => $request->appointment_time
                ]);

                $data['file_type'] = 'appointment_letters';
                $data['file'] = $appointmentLetter->id;
            }
        }

        ApplicationProgress::create($data);

        if ($request->has('redirect')) {
            return redirect($request->redirect)
                ->with('success', 'Application progress entry created successfully.');
        }

        return redirect()->route('admin.application-progress.index')
            ->with('success', 'Application progress entry created successfully.');
    }

    /**
     * Display the specified application progress entry.
     *
     * @param  \App\Models\ApplicationProgress  $applicationProgress
     * @return \Illuminate\Http\Response
     */
    public function show(ApplicationProgress $applicationProgress)
    {
        $applicationProgress->load(['customer', 'remarkedByUser']);
        return view('admin.application-progress.show', compact('applicationProgress'));
    }

    /**
     * Show the form for editing the specified application progress entry.
     *
     * @param  \App\Models\ApplicationProgress  $applicationProgress
     * @return \Illuminate\Http\Response
     */
    public function edit(ApplicationProgress $applicationProgress)
    {
        $customers = Customer::all();
        return view('admin.application-progress.edit', compact('applicationProgress', 'customers'));
    }

    /**
     * Update the specified application progress entry in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ApplicationProgress  $applicationProgress
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request, ApplicationProgress $applicationProgress)
    {
        $status = \App\Models\ApplicationStatus::find($request->status_id);
        $slug = $status?->slug;

        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'status_id' => 'required|exists:application_statuses,id',
            'status_date' => 'required|date',
            'remark' => 'nullable|string',
        ];

        if (in_array($slug, [
            'details_verification',
            'appointment_scheduled',
            'appointment_rescheduled1',
            'appointment_rescheduled2',
            'appointment_rescheduled3'
        ])) {
            $rules['file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        if (in_array($slug, [
            'appointment_scheduled',
            'appointment_rescheduled1',
            'appointment_rescheduled2',
            'appointment_rescheduled3'
        ])) {
            $rules['appointment_date'] = 'nullable|date';
            $rules['appointment_time'] = 'nullable';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        if (isset($data['remark']) && $data['remark'] !== $applicationProgress->remark) {
            $data['remarked_by'] = Auth::id();
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            if ($slug === 'details_verification') {
                $finalDetail = FinalDetail::create([
                    'customer_id' => $request->customer_id,
                    'file_path' => $filePath,
                    'upload_date' => now(),
                    'uploaded_by' => Auth::id(),
                    'is_approved' => false
                ]);

                $data['file_type'] = 'final_details';
                $data['file'] = $finalDetail->id;

            } elseif (in_array($slug, [
                'appointment_scheduled',
                'appointment_rescheduled1',
                'appointment_rescheduled2',
                'appointment_rescheduled3'
            ])) {

                $appointmentLetter = AppointmentLetter::create([
                    'customer_id' => $request->customer_id,
                    'file_path' => $filePath,
                    'upload_date' => now(),
                    'uploaded_by' => Auth::id(),
                    'appointment_date' => $request->appointment_date,
                    'appointment_time' => $request->appointment_time
                ]);

                $data['file_type'] = 'appointment_letters';
                $data['file'] = $appointmentLetter->id;
            }
        }

        $applicationProgress->update($data);

        return redirect()->route('admin.application-progress.index')
            ->with('success', 'Application progress entry updated successfully.');
    }
    /**
     * Remove the specified application progress entry from storage.
     *
     * @param  \App\Models\ApplicationProgress  $applicationProgress
     * @return \Illuminate\Http\Response
     */
    public function destroy(ApplicationProgress $applicationProgress, Request $request)
    {
        $customerId = $applicationProgress->customer_id;
        $applicationProgress->delete();
        
        // If the request is coming from the customer show page, redirect back there
        if ($request->has('redirect')) {
            return redirect($request->redirect)
                ->with('success', 'Application progress entry deleted successfully.');
        }
        
        // Otherwise, if we were in a customer view page, redirect to that customer
        if ($customerId && $request->has('from_customer') && $request->from_customer) {
            return redirect()->route('admin.customers.show', $customerId)
                ->with('success', 'Application progress entry deleted successfully.');
        }
        
        return redirect()->route('admin.application-progress.index')
            ->with('success', 'Application progress entry deleted successfully.');
    }

    /**
     * Display application progress history for a specific customer.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function customerHistory(Customer $customer)
    {
        $history = $customer->applicationProgress()
            ->with('remarkedByUser')
            ->orderBy('status_date', 'desc')
            ->get();
            
        return view('admin.application-progress.history', compact('customer', 'history'));
    }
} 