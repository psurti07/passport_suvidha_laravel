<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicationProgress;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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
        $userId = $user->id;
        
        // Get all progress entries for the customer
        $progressEntries = ApplicationProgress::where('customer_id', $userId)
            ->orderBy('status_date', 'asc')
            ->get();
            
        // Create stages array from actual database entries
        $stages = [];
        $lastCompletedStage = null;
        
        foreach ($progressEntries as $entry) {
            $stages[] = [
                'title' => $entry->application_status,
                'description' => $entry->remark ?? $this->getDefaultDescription($entry->application_status),
                'date' => $entry->status_date->format('F d, Y'),
                'completed' => true
            ];
            
            $lastCompletedStage = $entry->application_status;
        }
        
        // Calculate progress percentage
        $totalStagesExpected = 6; // Total expected stages in the process
        $completedStagesCount = count($stages);
        $progressPercentage = ($totalStagesExpected > 0) ? round(($completedStagesCount / $totalStagesExpected) * 100) : 0;
        
        // Calculate estimated completion date
        $estimatedCompletionDate = null;
        $finalStage = $progressEntries->last();
        if ($finalStage && $finalStage->status_date) {
            // If we already have a final stage with final_approval
            if ($finalStage->application_status === 'final_approval') {
                $estimatedCompletionDate = $finalStage->status_date->format('F d, Y');
            } else {
                // Estimate based on last update + 10 days
                $estimatedCompletionDate = $finalStage->status_date->addDays(10)->format('F d, Y');
            }
        }
        
        return response()->json([
            'progress_percentage' => $progressPercentage,
            'estimated_completion' => $estimatedCompletionDate,
            'stages' => $stages,
            'current_stage' => $lastCompletedStage
        ]);
    }
       
} 