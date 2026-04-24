<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfferOrder;
use Yajra\DataTables\Facades\DataTables;

class CardOfferController extends Controller
{
    public function index()
    {
        return view('admin.card-offers.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = OfferOrder::query()
            ->where('offer_type', '1')
            ->select([
                'id',
                'full_name',
                'mobile',
                'email',
                'card_number',
                'amount',
                'payment_id',
                'is_customer',
                'created_at'
            ]);

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

        if ($request->filled('is_customer')) {
            $query->where('is_customer', $request->is_customer);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('is_customer', function ($row) {
                return $row->is_customer ? '1' : '0';
            })

            ->editColumn('amount', function ($row) {
                return '₹ ' . number_format($row->amount, 2);
            })

            ->editColumn('payment_id', function ($row) {
                return $row->payment_id
                    ? $row->payment_id
                    : '-';
            })

            ->editColumn('card_number', function ($row) {
                return $row->card_number ?? '-';
            })

            ->editColumn('is_customer', function ($row) {
                if ($row->is_customer == '1') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">Customer</span>';
                } else {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-red-100 text-red-800">Lead</span>';
                }
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i:s');
            })

            ->addColumn('actions', function ($row) {
                $html = '<div class="flex space-x-2">';
                if (!$row->is_customer) {
                    $html .= '
                        <form action="' . route('admin.card-offers.customer', $row->id) . '"
                            method="POST" class="inline">
                            ' . csrf_field() . '
                            ' . method_field('PATCH') . '
                            <button type="submit"
                                class="text-green-600 hover:text-green-900 flex"
                                title="Customer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Customer
                            </button>
                        </form>
                    ';
                } else {
                    $html .= '
                        <form action="' . route('admin.card-offers.lead', $row->id) . '"
                            method="POST" class="inline">
                            ' . csrf_field() . '
                            ' . method_field('PATCH') . '
                            <button type="submit"
                                class="text-red-600 hover:text-red-900 flex"
                                title="Lead">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Lead
                            </button>
                        </form>
                    ';
                }

                $html .= '</div>';

                return $html;
            })

            ->rawColumns(['payment_id', 'card_number', 'is_customer', 'actions'])

            ->make(true);
    }

    public function customer(OfferOrder $cardOffer)
    {
        $cardOffer->update([
            'is_customer' => 1,
        ]);

        return redirect()->route('admin.card-offers.index')
            ->with('success', 'Lead converted to customer successfully.');
    }

    public function lead(OfferOrder $cardOffer)
    {
        $cardOffer->update([
            'is_customer' => 0,
        ]);

        return redirect()->route('admin.card-offers.index')
            ->with('success', 'Customer converted to lead successfully.');
    }
}
