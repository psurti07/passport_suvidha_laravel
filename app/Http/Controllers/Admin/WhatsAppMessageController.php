<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ApplicationProgress;
use App\Models\ApplicationStatus;
use App\Models\FinalDetail;
use App\Models\AppointmentLetter;
use App\Services\InteraktService;
use App\Services\SecureDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppMessageController extends Controller
{
    public function __construct(
        protected InteraktService $interaktService,
        protected SecureDocumentService $secureDocumentService
    ) {}

    /**
     * Send document through WhatsApp.
     */
    public function sendDocument(Request $request, $progressId)
    {
        try {

            $progress = ApplicationProgress::with('status')
                ->find($progressId);

            if (!$progress) {
                return response()->json([
                    'status' => false,
                    'message' => 'Application progress not found.',
                ], 404);
            }

            $customer = Customer::find($progress->customer_id);
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found.',
                ], 404);
            }

            $status = ApplicationStatus::find($progress->status_id);

            if (!$status) {
                return response()->json([
                    'status' => false,
                    'message' => 'Application status not found.',
                ], 404);
            }

            $statusSlug = $status->slug;

            $allowedStatuses = [
                'details_verification',
                'appointment_scheduled',
                // 'appointment_rescheduled1',
                // 'appointment_rescheduled2',
                // 'appointment_rescheduled3',
            ];

            if (!in_array($statusSlug, $allowedStatuses, true)) {
                return response()->json([
                    'status' => false,
                    'message' => 'WhatsApp document is not available for the current status.',
                ], 422);
            }

            if ($statusSlug === 'details_verification') {

                $document = FinalDetail::where(
                    'customer_id',
                    $customer->id
                )
                    ->latest('id')
                    ->first();

                if (!$document) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Final details document not found.',
                    ], 404);
                }

                if (!$this->secureDocumentService->fileExists($document)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Final details file does not exist.',
                    ], 404);
                }

                $documentUrl = $this->secureDocumentService->generateUrl(
                    'final_details',
                    $document->id
                );
            } else {

                $document = AppointmentLetter::where(
                    'customer_id',
                    $customer->id
                )
                    ->latest('id')
                    ->first();

                if (!$document) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Appointment letter not found.',
                    ], 404);
                }

                if (!$this->secureDocumentService->fileExists($document)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Appointment letter file does not exist.',
                    ], 404);
                }

                $documentUrl = $this->secureDocumentService->generateUrl(
                    'appointment_letters',
                    $document->id
                );
            }

            if (!$customer->mobile_number) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer mobile number not found.',
                ], 422);
            }

            $customerName = $customer->full_name ?: 'Customer';

            $templateType = $statusSlug === 'details_verification'
                ? 'details_verification'
                : 'appointment';

            $result = $this->interaktService->sendDocument(
                $customer->mobile_number,
                $customerName,
                $documentUrl,
                $templateType
            );

            if (!$result['status']) {

                Log::error('WhatsApp document sending failed', [
                    'customer_id' => $customer->id,
                    'progress_id' => $progress->id,
                    'status_id' => $status->id,
                    'status_slug' => $statusSlug,
                    'response' => $result,
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $result['message']
                        ?? 'WhatsApp document sending failed.',
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'WhatsApp message sent successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('WHATSAPP MESSAGE ERROR', [
                'progress_id' => $progressId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Unable to send WhatsApp message.',
            ], 500);
        }
    }
}
