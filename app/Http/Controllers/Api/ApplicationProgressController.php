<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\ApplicationProgress;
use App\Models\ApplicationStatus;
use App\Models\Customer;
use App\Models\FinalDetail;
use App\Models\Invoice;
use App\Models\RazorpayLog;
use App\Models\Service;
use App\Services\InteraktService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\SecureDocumentService;

class ApplicationProgressController extends Controller
{

    /**
     * Get a structured view of the customer's application progress for the frontend.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerApplicationStatus(SecureDocumentService $documentService)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $userId = $user->id;

        $progressEntries = ApplicationProgress::with([
            'status',
            'finalDetail',
            'appointmentLetter',
        ])
            ->where('customer_id', $userId)
            ->orderBy('status_date', 'asc')
            ->get();

        $stages = [];

        $lastCompletedStage = null;

        foreach ($progressEntries as $entry) {

            $hasFile = false;

            $fileToken = null;

            if (
                $entry->file_type === 'final_details' &&
                $entry->finalDetail &&
                !empty($entry->finalDetail->file_path)
            ) {
                $hasFile = true;

                $fileToken = $documentService->generateToken(
                    'final_details',
                    $entry->finalDetail->id
                );
            } elseif (
                $entry->file_type === 'appointment_letters' &&
                $entry->appointmentLetter &&
                !empty($entry->appointmentLetter->file_path)
            ) {
                $hasFile = true;

                $fileToken = $documentService->generateToken(
                    'appointment_letters',
                    $entry->appointmentLetter->id
                );
            }

            $statusSlug = $entry->status->slug ?? null;

            $statusName = $entry->status->status_name ?? null;

            $stages[] = [

                'id' => $entry->id,

                'title' => $statusSlug,

                'status_name' => $statusName,

                'colorclass' =>
                $entry->status->colorclass ?? 'gray',

                'remark' =>
                $entry->remark
                    ?? $this->getDefaultDescription(
                        $entry->application_status
                    ),

                'status_date' =>
                optional($entry->status_date)->toISOString(),

                'formatted_date' =>
                optional($entry->status_date)->format('F d, Y'),

                'completed' => true,

                'file_type' =>
                $entry->file_type,

                'has_file' =>
                $hasFile,

                'file_token' =>
                $fileToken,
            ];
            $lastCompletedStage = $statusSlug;
        }

        $firstEntry = $progressEntries->first();

        $finalStage = $progressEntries->last();

        $totalStagesExpected = 6;

        $completedStagesCount = count($stages);

        $progressPercentage = $totalStagesExpected > 0
            ? min(
                100,
                round(
                    ($completedStagesCount / $totalStagesExpected) * 100
                )
            )
            : 0;

        $estimatedCompletionDate = null;

        if ($finalStage && $finalStage->status_date) {

            $finalStatusSlug = $finalStage->status->slug ?? null;

            if ($finalStatusSlug === 'final_approval') {

                $estimatedCompletionDate =
                    $finalStage->status_date->format('F d, Y');
            } else {

                $estimatedCompletionDate =
                    $finalStage->status_date
                    ->copy()
                    ->addDays(10)
                    ->format('F d, Y');
            }
        }

        return response()->json([

            'progress_percentage' =>
            $progressPercentage,

            'estimated_completion' =>
            $estimatedCompletionDate,

            'stages' =>
            $stages,

            'current_stage' =>
            $lastCompletedStage,

            'created_at' =>
            $firstEntry && $firstEntry->created_at
                ? $firstEntry->created_at->toISOString()
                : null,

            'updated_at' =>
            $finalStage && $finalStage->updated_at
                ? $finalStage->updated_at->toISOString()
                : null,
        ]);
    }

    public function file(
        string $encryptedId,
        SecureDocumentService $documentService
    ) {
        try {
            $payload = $documentService->decryptToken($encryptedId);

            if (!$payload) {
                return redirect()->away(
                    config('app.frontend_url', 'https://passportsuvidha.com')
                );
            }

            $fileRecord = $documentService->getFileRecord($payload);

            if (!$fileRecord) {
                return redirect()->away(
                    config('app.frontend_url', 'https://passportsuvidha.com')
                );
            }

            if (!$documentService->fileExists($fileRecord)) {
                return redirect()->away(
                    config('app.frontend_url', 'https://passportsuvidha.com')
                );
            }

            $disk = Storage::disk('public');

            $mimeType = $disk->mimeType(
                $fileRecord->file_path
            ) ?: 'application/octet-stream';

            $fileSize = $disk->size(
                $fileRecord->file_path
            );

            $stream = $disk->readStream(
                $fileRecord->file_path
            );

            if (!$stream) {
                return redirect()->away(
                    config('app.frontend_url', 'https://passportsuvidha.com')
                );
            }

            $filename = basename(
                $fileRecord->file_path
            );

            return response()->stream(
                function () use ($stream) {
                    fpassthru($stream);

                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                },
                200,
                [
                    'Content-Type' => $mimeType,

                    'Content-Length' => $fileSize,

                    'Content-Disposition' =>
                    'inline; filename="' .
                        addslashes($filename) .
                        '"',

                    'Cache-Control' =>
                    'private, no-store, no-cache, must-revalidate',

                    'Pragma' => 'no-cache',

                    'Expires' => '0',

                    'X-Content-Type-Options' => 'nosniff',
                ]
            );
        } catch (\Throwable $e) {

            Log::warning('Secure document access failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->away(
                config('app.frontend_url', 'https://passportsuvidha.com')
            );
        }
    }
    public function details(Request $request)
    {
        $user = $request->user();

        $customer = Customer::find($user->id);

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found'
            ], 404);
        }

        $service = $customer->service;

        $invoice = Invoice::where('customer_id', $customer->id)->latest()->first();

        $paymentLog = RazorpayLog::where('customer_id', $customer->id)->where('tx_status', 'success')->latest()->first();

        $progress = ApplicationProgress::where('customer_id', $customer->id)->get();

        $statuses = ApplicationStatus::orderBy('id')->get();

        // map stages
        $stages = [];
        $currentStage = null;

        foreach ($statuses as $status) {
            $stageData = $progress->firstWhere('status_id', $status->id);

            $completed = $stageData ? true : false;

            $stages[] = [
                'title' => $status->slug,
                'label' => $status->status_name,
                'completed' => $completed,
                'date' => $stageData ? $stageData->created_at : null,
            ];

            // Pick first incomplete stage as current, fallback to last completed
            if (!$currentStage && !$completed) {
                $currentStage = [
                    'title' => $status->slug,
                    'label' => $status->status_name,
                    'completed' => false,
                    'date' => null,
                ];
            }
        }

        // If all stages completed, current stage = last stage
        if (!$currentStage && !empty($stages)) {
            $currentStage = end($stages);
        }

        // progress %
        $completedCount = collect($stages)->where('completed', true)->count();
        $totalStages = count($stages);

        return response()->json([
            'customer' => $customer,
            'service' => $service,
            'invoice' => $invoice,
            'payment' => $paymentLog,
            'progress' => [
                'percentage' => ($totalStages > 0) ? round(($completedCount / $totalStages) * 100) : 0,
                'stages' => $stages,
                'current_stage' => $currentStage
            ]
        ]);
    }

    // public function getApplicationProgress()
    // {
    //     $customerId = auth()->id();

    //     $data = DB::table('application_statuses as s')
    //         ->join('application_progress as p', function ($join) use ($customerId) {
    //             $join->on('p.status_id', '=', 's.id')
    //                 ->where('p.customer_id', '=', $customerId)
    //                 ->whereNotNull('p.remark')
    //                 ->where('p.remark', '!=', '');
    //         })
    //         ->select(
    //             's.status_name',
    //             's.slug',
    //             'p.remark',
    //             'p.status_date',
    //             'p.file',
    //             'p.file_type'
    //         )
    //         ->orderBy('s.step', 'ASC')
    //         ->get();

    //     return response()->json([
    //         'status' => true,
    //         'data' => $data
    //     ]);
    // }


    public function getApplicationProgress(SecureDocumentService $documentService)
    {
        $customerId = Auth::id();

        $data = ApplicationProgress::with([
            'status',
            'finalDetail',
            'appointmentLetter',
        ])
            ->where('customer_id', $customerId)
            ->whereNotNull('remark')
            ->where('remark', '!=', '')
            ->get()
            ->sortBy('status.step')
            ->values();

        $data = $data->map(function ($item) {

            $hasFile = false;
            $fileToken = null;

            if (
                $item->file_type === 'final_details' &&
                $item->finalDetail &&
                !empty($item->finalDetail->file_path)
            ) {
                $hasFile = true;

                $fileToken = $documentService->generateToken(
                    'final_details',
                    $item->finalDetail->id
                );
            } elseif (
                $item->file_type === 'appointment_letters' &&
                $item->appointmentLetter &&
                !empty($item->appointmentLetter->file_path)
            ) {
                $hasFile = true;

                $fileToken = $documentService->generateToken(
                    'appointment_letters',
                    $item->appointmentLetter->id
                );
            }

            return [
                'id' => $item->id,

                'status_name' =>
                $item->status->status_name ?? null,

                'slug' =>
                $item->status->slug ?? null,

                'colorclass' =>
                $item->status->colorclass ?? 'gray',

                'remark' =>
                $item->remark,

                'status_date' =>
                $item->status_date,

                'file_type' =>
                $item->file_type,

                'has_file' =>
                $hasFile,

                'file_token' =>
                $fileToken,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    // show all status
    // public function getStatusByMobile(Request $request){
    //     Log::info('Received request to get application status by mobile', ['mobile' => $request->toArray()]); 
    //     $mobile = $request['mobile'];    
    //     $customer = Customer::where('mobile_number', $mobile)->first();

    //     if (!$customer) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Customer not found'
    //         ], 404);
    //     }

    //     $name = "$customer->full_name";

    //     $progressEntries = ApplicationProgress::with('status')
    //         ->where('customer_id', $customer->id)
    //         ->orderBy('status_date', 'asc')
    //         ->get();

    //     $stages = [];
    //     foreach ($progressEntries as $entry) {
    //         $stages[] = [
    //             'title' => $entry->status->slug ?? null,
    //             'description' => $entry->remark ?? null,
    //             'date' => optional($entry->status_date)->toISOString(),
    //             'formatted_date' => optional($entry->status_date)->format('F d, Y'),
    //             'completed' => true,
    //         ];
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => [
    //             'customer_name' => $name,
    //             'mobile' => $customer->mobile_number,
    //             'stages' => $stages
    //         ]
    //     ], 200);
    // }

    public function getStatusByMobile(Request $request)
    {
        $mobile = $request['mobile'];

        $customer = Customer::where('mobile_number', $mobile)->first();

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found'
            ], 404);
        }

        $name = $customer->full_name;

        $latestProgress = ApplicationProgress::where('customer_id', $customer->id)->orderByDesc('id')->first();
        $currentStage = null;

        if ($latestProgress) {

            $status = ApplicationStatus::where('id', $latestProgress->status_id)->first();

            $currentStage = [
                'title' => $status->slug ?? null,
                'description' => $latestProgress->remark ?? null,
                'date' => $latestProgress->status_date,
                'formatted_date' => $latestProgress->status_date
                    ? \Carbon\Carbon::parse($latestProgress->status_date)
                    ->format('F d, Y')
                    : null,
                'completed' => true,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'customer_name' => $name,
                'mobile' => $customer->mobile_number,
                'current_stage' => $currentStage
            ]
        ], 200);
    }
}
