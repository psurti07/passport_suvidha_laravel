<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RazorpayLog;
use Yajra\DataTables\Facades\DataTables;

class RazorpayLogController extends Controller
{
    public function index()
    {
        return view('admin.razorpay-logs.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = RazorpayLog::select([
            'id',
            'customer_id',
            'order_id',
            'order_amount',
            'order_note',
            'reference_id',
            'payment_id',
            'tx_status',
            'payment_mode',
            'service_type',
            'offer_type',
            'created_at'
        ]);

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

        if ($request->filled('tx_status')) {
            $query->where('tx_status', $request->tx_status);
        }

        $logs = $query->get();

        $applicationIds = [];
        $offerIds = [];

        foreach ($logs as $log) {
            if (!empty($log->customer_id)) {
                $applicationIds[] = $log->order_id;
            } else {
                $offerIds[] = $log->order_id;
            }
        }

        $appOrders = \App\Models\ApplicationOrder::with('customer')
            ->whereIn('id', $applicationIds)
            ->get()
            ->keyBy('id');

        $offerOrders = \App\Models\OfferOrder::whereIn('id', $offerIds)
            ->get()
            ->keyBy('id');

        return DataTables::of($logs)

            ->addIndexColumn()

            // ->addColumn('customer_name', function ($row) use ($appOrders, $offerOrders) {

            //     if (!empty($row->customer_id)) {

            //         $order = $appOrders[$row->order_id] ?? null;
            //         $customer = $order->customer ?? null;

            //         $fullName = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
            //         $email = $customer->email ?? '-';

            //         return "
            //             <div>
            //                 <div class='font-semibold text-gray-900'>" . ($fullName ?: '-') . "</div>
            //                 <div class='text-xs text-gray-500'>{$email}</div>
            //             </div>
            //         ";
            //     }

            //     $offer = $offerOrders[$row->order_id] ?? null;

            //     return "
            //         <div>
            //             <div class='font-semibold text-gray-900'>" . ($offer->full_name ?? '-') . "</div>
            //             <div class='text-xs text-gray-500'>" . ($offer->email ?? '-') . "</div>
            //         </div>
            //     ";
            // })

            // ->addColumn('customer_mobile_number', function ($row) use ($appOrders, $offerOrders) {

            //     if (!empty($row->customer_id)) {
            //         return $appOrders[$row->order_id]->customer->mobile_number ?? '-';
            //     }

            //     return $offerOrders[$row->order_id]->mobile ?? '-';
            // })

            // ->addColumn('type', function ($row) {

            //     if (!empty($row->customer_id)) {
            //         return $row->service_text; 
            //     }

            //     return $row->offer_text; 
            // })
            ->addColumn('customer_name', function ($row) use ($offerOrders) {

                if ($row->offer_type) {

                    $offer = $offerOrders[$row->order_id] ?? null;

                    return "
                        <div>
                            <div class='font-semibold text-gray-900'>" . ($offer->full_name ?? '-') . "</div>
                            <div class='text-xs text-gray-500'>" . ($offer->email ?? '-') . "</div>
                        </div>
                    ";
                }

                if ($row->customer_id) {

                    $customer = \App\Models\Customer::find($row->customer_id);

                    $fullName = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));

                    return "
                        <div>
                            <div class='font-semibold text-gray-900'>" . ($fullName ?: '-') . "</div>
                            <div class='text-xs text-gray-500'>" . ($customer->email ?? '-') . "</div>
                        </div>
                    ";
                }

                return '-';
            })

            ->addColumn('customer_mobile_number', function ($row) use ($offerOrders) {

                if ($row->offer_type) {
                    $offer = $offerOrders[$row->order_id] ?? null;
                    return $offer->mobile ?? '-';
                }

                if ($row->customer_id) {
                    $customer = \App\Models\Customer::find($row->customer_id);
                    return $customer->mobile_number ?? '-';
                }

                return '-';
            })

            ->addColumn('type', function ($row) {

                if ($row->offer_type) {
                    return $row->offer_text;
                }

                return $row->service_text;
            })

            ->editColumn('payment_id', function ($row) {
                return $row->payment_id ?: '-';
            })

            ->editColumn('payment_mode', function ($row) {
                return $row->payment_mode ?: '-';
            })

            ->editColumn('tx_status', function ($row) {

                if (!$row->tx_status) {
                    return '<span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-800 rounded">N/A</span>';
                }

                return match ($row->tx_status) {
                    'pending' => '<span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">Pending</span>',
                    'failed'  => '<span class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded">Failed</span>',
                    'success' => '<span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded">Success</span>',
                    default   => '<span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-800 rounded">' . ucfirst($row->tx_status) . '</span>',
                };
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i:s');
            })

            ->rawColumns(['customer_name', 'tx_status'])

            ->make(true);
    }
}
