<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\ScheduleSlot;
use App\Models\Service;
use Carbon\Carbon;
use Exception;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\Log;

use function Deployer\timestamp;

class SchedualSlotController extends Controller
{
    public function getScheduleDetails(Request $request)
    {
        try {
            $encryptId = $request->id;

            if (!$encryptId) {
                return response([
                    'success' => false,
                    'message' => "Invalid Link"
                ]);
            }

            $customer_id =  decryptData($encryptId);

            $customer = Customer::find($customer_id);
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => "Customer not found"
                ], 404);
            }

            $schedule = ScheduleSlot::where([
                'customer_id' => $customer_id,
                'service_id' => $customer->service_id,
                'status' => ScheduleSlot::SCHEDULED
            ])->whereNull('deleted_at')->first();

            if (!empty($schedule)) {
                return response()->json([
                    'success' => true,
                    'already_scheduled' => true,
                    'schedule_id' => encryptData($schedule->id)
                ]);
            }

            return response()->json([
                'success' => true,
                'customer' => $customer
            ]);
        } catch (\Exception $e) {
            Log::error('GET SCHEDULE DETAILS ERROR', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function scheduleSlot(Request $request)
    {
        $request->validate([
            'customerId' => 'required',
            'date' => 'required:date',
            'language' => 'required:in' . implode(',', ScheduleSlot::getLanguages()),
            'slot' => 'required'
        ]);

        try {

            $encryptId = $request->customerId;
            $language = $request->language;

            $customer_id = decryptData($encryptId);
            $customer = Customer::find($customer_id);
            $service_id = $customer->service_id;

            $schedule = ScheduleSlot::create([
                'customer_id' => $customer_id,
                'service_id' => $service_id,
                'date' => Carbon::parse($request->date),
                'time' => Carbon::parse($request->slot),
                'language' => ScheduleSlot::getLanguageId($language),
                'status' => ScheduleSlot::SCHEDULED,
            ]);

            return response([
                'success' => true,
                'message' => 'schedule successully',
                'schedule_id' => encryptData($schedule->id),
            ]);
        } catch (Exception $e) {
            return response([
                'success' => false,
                'error' => "Error: " . $e->getMessage(),
            ]);
        }
    }

    public function scheduleSuccess(Request $request)
    {

        $request->validate([
            'id' => 'required'
        ]);

        $id = decryptData($request->id);

        $schedule = ScheduleSlot::find($id);

        if (!$schedule) {
            return response([
                'success' => false,
                'message' => "Schedule Slot not found",
            ], 404);
        }

        $customer_id = encryptData($schedule->customer_id);

        return response([
            'success' => true,
            'data' => $schedule,
            'schedule_id' => encryptData($schedule->id)
        ]);
    }

    public function scheduleCancle(Request $request)
    {
        try {

            $request->validate([
                'id' => 'required',
            ]);

            $schedule_id = decryptData($request->id);

            $schedule = ScheduleSlot::find($schedule_id);

            if (!$schedule) {
                return response([
                    'success' => false,
                    'message' => "Schedule Slot not found",
                ], 404);
            }

            $schedule->update([
                'status' => ScheduleSlot::CANCELLED,
                'deleted_at' => now()
            ]);

            $customer_id = encryptData($schedule->customer_id);

            return response([
                'success' => true,
                'customer_id' => $customer_id
            ]);
        } catch (Exception $e) {
            return response([
                'success' => false,
                "error" => $e->getMessage(),
            ]);
        }
    }

    // public function encryptId()
    // {
    //     $id = encryptData('5');
    //     return response()->json([
    //         'success' => true,
    //         'id' => $id
    //     ]);
    // }
    // public function decryptId()
    // {
    //     $id = decryptData('QEBAQCYmJiYjIyMjJCQkJBYdHUqeJS_tNwEVSORMaKg');
    //     return response()->json([
    //         'success' => true,
    //         'id' => $id
    //     ]);
    // }
}
