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
use App\Services\SmsService;
use App\Services\BrevoMailService;
use Barryvdh\DomPDF\Facade\Pdf;

class ApplicationProgressController extends Controller
{
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
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        $applicationProgress = $query->latest()->paginate(15);
        return view('admin.application-progress.index', compact('applicationProgress'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('admin.application-progress.create', compact('customers'));
    }

    public function store(Request $request, SmsService $smsService, BrevoMailService $brevoMailService)
    {
        $status = ApplicationStatus::find($request->status_id);
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


        $progress = ApplicationProgress::create($data);

        $customer = Customer::find($request->customer_id);

        if ($customer && !empty($customer->mobile_number) && $status) {

            $templateSlug = match ($status->slug) {
                'in_process' => 'application-in-process-sms',
                'documents_submitted' => 'documents-submitted-sms',
                'details_verification' => 'details-verification-sms',
                'appointment_scheduled' => 'appointment-scheduled-sms',
                'appointment_rescheduled1', 'appointment_rescheduled2', 'appointment_rescheduled3' => 'appointment-rescheduled-sms',
                'pov_success' => 'pov-success-sms',
                'pov_failed' => 'pov-failed-sms',
                'pov_insufficient_documents' => 'pov-insufficient-documents-sms',
                default => null
            };

            if ($templateSlug) {
                $smsService->sendTemplateSms(
                    $customer->mobile_number,
                    $templateSlug
                );
            }
        }

        if ($customer && $customer->email) {
            $attachments = [];
            if (
                $progress->file_type === 'final_details'
            ) {
                $finalDetail =
                    FinalDetail::find(
                        $progress->file
                    );

                if ($finalDetail) {
                    $attachments[] =
                        $this->prepareAttachment(
                            $finalDetail->file_path,
                            'final-details'
                        );
                }
            }

            if (
                $progress->file_type ===
                'appointment_letters'
            ) {
                $appointmentLetter =
                    AppointmentLetter::find(
                        $progress->file
                    );
                if ($appointmentLetter) {
                    $attachments[] =
                        $this->prepareAttachment(
                            $appointmentLetter->file_path,
                            'appointment-letter'
                        );
                }
            }

            $html = "
                <h2>
                Dear {$customer->first_name} {$customer->last_name} 
                </h2>

                <p>
                Your application status has been updated.
                </p>

                <p>
                <b>Status:</b>
                {$status->status_name}
                </p>

                <p>
                <b>Remark:</b>
                {$request->remark}
                </p>

                <br>

                Thanks,<br>
                Passport Suvidha

                ";

            if (!empty($attachments)) {
                $brevoMailService
                    ->sendBrevoHtmlMailWithAttachments(
                        $customer->email,
                        $customer->first_name,
                        'Application Status Update',
                        $html,
                        $attachments
                    );
            } else {
                $brevoMailService
                    ->sendBrevoHtmlMail(
                        $customer->email,
                        $customer->first_name,
                        'Application Status Update',
                        $html
                    );
            }
        }

        if ($status && $status->step) {
            $customer = Customer::find($request->customer_id);
            if ($customer) {
                $customer->update([
                    'registration_step' => $status->step
                ]);
            }
        }

        if ($request->has('redirect')) {
            return redirect($request->redirect)
                ->with('success', 'Application progress entry created successfully.');
        }

        return redirect()->route('admin.application-progress.index')
            ->with('success', 'Application progress entry created successfully.');
    }

    private function prepareAttachment($path, $name)
    {
        $filePath = storage_path(
            'app/public/' . $path
        );

        if (!file_exists($filePath)) {
            return null;
        }

        $extension = strtolower(
            pathinfo($filePath, PATHINFO_EXTENSION)
        );

        if ($extension === 'pdf') {
            return [
                'name' => $name . '.pdf',
                'content' => base64_encode(
                    file_get_contents($filePath)
                )

            ];
        }

        $pdf = Pdf::loadView(
            'pdf.document-preview',
            [
                'file' => $filePath
            ]
        );

        return [
            'name' => $name . '.pdf',
            'content' => base64_encode(
                $pdf->output()
            )

        ];
    }

    public function show(ApplicationProgress $applicationProgress)
    {
        $applicationProgress->load(['customer', 'remarkedByUser']);
        return view('admin.application-progress.show', compact('applicationProgress'));
    }

    public function edit(ApplicationProgress $applicationProgress)
    {
        $customers = Customer::all();
        return view('admin.application-progress.edit', compact('applicationProgress', 'customers'));
    }

    public function update(Request $request, ApplicationProgress $applicationProgress)
    {
        $status = ApplicationStatus::find($request->status_id);
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

        if ($status && $status->step) {
            $customer = Customer::find($request->customer_id);
            if ($customer) {
                $customer->update([
                    'registration_step' => $status->step
                ]);
            }
        }

        return redirect()->route('admin.application-progress.index')
            ->with('success', 'Application progress entry updated successfully.');
    }

    public function destroy(ApplicationProgress $applicationProgress, Request $request)
    {
        $customerId = $applicationProgress->customer_id;
        $applicationProgress->delete();

        if ($request->has('redirect')) {
            return redirect($request->redirect)
                ->with('success', 'Application progress entry deleted successfully.');
        }

        if ($customerId && $request->has('from_customer') && $request->from_customer) {
            return redirect()->route('admin.customers.show', $customerId)
                ->with('success', 'Application progress entry deleted successfully.');
        }

        return redirect()->route('admin.application-progress.index')
            ->with('success', 'Application progress entry deleted successfully.');
    }

    public function customerHistory(Customer $customer)
    {
        $history = $customer->applicationProgress()
            ->with('remarkedByUser')
            ->orderBy('status_date', 'desc')
            ->get();

        return view('admin.application-progress.history', compact('customer', 'history'));
    }
}
