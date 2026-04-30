<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicationProgress;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicationProgressController extends Controller
{     
   
    /**
     * Get a structured view of the customer's application progress for the frontend.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerApplicationStatus()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $userId = $user->id;

        // Get all progress entries
        // $progressEntries = ApplicationProgress::where('customer_id', $userId)
        //     ->orderBy('status_date', 'asc')
        //     ->get();

        $progressEntries = ApplicationProgress::with('status')
        ->where('customer_id', $userId)
        ->orderBy('status_date', 'asc')
        ->get();

        $stages = [];
        $lastCompletedStage = null;

        foreach ($progressEntries as $entry) {
            $stages[] = [
                'title' => $entry->status->slug ?? null,
                'description' => $entry->remark ?? $this->getDefaultDescription($entry->application_status),
                'date' => optional($entry->status_date)->toISOString(),
                'formatted_date' => optional($entry->status_date)->format('F d, Y'),
                'completed' => true,
            ];

            $lastCompletedStage = $entry->application_status;
        }

        $firstEntry = $progressEntries->first();

        $totalStagesExpected = 6;
        $completedStagesCount = count($stages);

        $progressPercentage = $totalStagesExpected > 0
            ? round(($completedStagesCount / $totalStagesExpected) * 100)
            : 0;

        $estimatedCompletionDate = null;
        $finalStage = $progressEntries->last();

        if ($finalStage && $finalStage->status_date) {
            if ($finalStage->application_status === 'final_approval') {
                $estimatedCompletionDate = $finalStage->status_date->format('F d, Y');
            } else {
                $estimatedCompletionDate = $finalStage->status_date
                ->copy()
                ->addDays(10)
                ->format('F d, Y');
            }
        }

        return response()->json([
            'progress_percentage' => $progressPercentage,
            'estimated_completion' => $estimatedCompletionDate,
            'stages' => $stages,
            'current_stage' => $lastCompletedStage,

            'created_at' => optional($firstEntry?->created_at)->toISOString(),

            'updated_at' => optional($finalStage?->updated_at)->toISOString(),
        ]);
    }

    public function details(Request $request)
    {
        $user = $request->user();

        $customer = Customer::find($user->id);

        $service = DB::table('services')
            ->where('id', $customer->service_id)
            ->first();

        $invoice = DB::table('invoices')
            ->where('customer_id', $customer->id)
            ->latest()
            ->first();

        $paymentLog = DB::table('razorpay_logs_entry')
            ->where('customer_id', $customer->id)
            ->where('tx_status', 'success')
            ->latest()
            ->first();

        $progress = DB::table('application_progress')
            ->where('customer_id', $customer->id)
            ->get();

        $statuses = DB::table('application_statuses')->orderBy('id')->get();

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

    public function getApplicationProgress()
    {
        $customerId = auth()->id();

        $data = ApplicationProgress::with([
                'status',
                'finalDetail',
                'appointmentLetter'
            ])
            ->where('customer_id', $customerId)
            ->whereNotNull('remark')
            ->where('remark', '!=', '')
            ->get()
            ->sortBy('status.step') // sort by step
            ->values();

        // Format response
        $data = $data->map(function ($item) {

            $filePath = null;
            Log::info('Processing application progress item', [
                'id' => $item->id,
                'file_type' => $item->file_type,
                'file' => $item->file,
                'finalDetail' => $item->finalDetail ? $item->finalDetail->toArray() : null,
                'appointmentLetter' => $item->appointmentLetter ? $item->appointmentLetter->toArray() : null,
            ]);
            if ($item->file_type === 'final_details' && $item->finalDetail) {
                $filePath = $item->finalDetail->file_path;
            } elseif ($item->file_type === 'appointment_letters' && $item->appointmentLetter) {
                $filePath = $item->appointmentLetter->file_path;
            } else {
                $filePath = $item->file;
            }

            return [
                'status_name' => $item->status->status_name ?? null,
                'slug'        => $item->status->slug ?? null,
                'colorclass'  => $item->status->colorclass ?? 'gray', // ✅ ADD THIS
                'remark'      => $item->remark,
                'status_date' => $item->status_date,
                'file_type'   => $item->file_type,
                'file_url'    => $filePath ? asset('/storage/' . $filePath) : null,
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $data
        ]);
    }
} 