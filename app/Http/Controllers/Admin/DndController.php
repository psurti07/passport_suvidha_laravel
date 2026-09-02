<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Service;
use Yajra\DataTables\Facades\DataTables;

class DndController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('admin.dnd.index', compact('services'));
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = Customer::with('service')->select([
            'id',
            'service_id',
            'full_name',
            'mobile_number',
            'is_dnd',
            'updated_at'
        ])->where('is_dnd', 1);

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('updated_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

        if ($request->service) {
            $query->where('service_id', $request->service);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('service_name', function ($row) {
                return $row->service ? $row->service->service_name : 'N/A';
            })

            ->addColumn('customer_name', function ($row) {
                return $row->full_name;
            })

            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->format('d M Y, h:i A');
            })

            ->filterColumn('service_name', function ($query, $keyword) {
                $query->whereHas('service', function ($q) use ($keyword) {
                    $q->where('service_name', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('customer_name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('full_name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('actions', function ($row) {
                return '
                    <!-- Delete -->
                    <form action="' . route('admin.dnd.destroy', $row->id) . '" method="POST" class="inline">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="button" 
                            onclick="confirmDelete(\'' . $row->full_name . ' Customer\', this.form)"
                            class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 hover:border-red-600 transition-all duration-200 shadow-sm" 
                            title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                        </button>
                    </form>
                ';
            })

            ->rawColumns(['service_name', 'actions'])

            ->make(true);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'dnd_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('dnd_file');

        $mobiles = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {

            $rowNumber = 0;

            while (($row = fgetcsv($handle, 1000, ",")) !== false) {

                $rowNumber++;

                if ($rowNumber == 1) { // Skip header
                    continue;
                }

                $mobile = trim($row[0] ?? '');

                if (preg_match('/^[6-9]\d{9}$/', $mobile)) {
                    $mobiles[] = $mobile;
                }
            }

            fclose($handle);
        }

        if (count($mobiles) > 0) {

            Customer::whereIn('mobile_number', $mobiles)
                ->update(['is_dnd' => 1]);

            return redirect()->route('admin.dnd.index')
                ->with('success', count($mobiles) . ' numbers added to DND list.');
        }

        return redirect()->back()->with('error', 'No valid numbers found.');
    }

    public function destroy(Customer $customer)
    {
        Customer::where('id', $customer->id)->update(['is_dnd' => 0]);
        return redirect()->route('admin.dnd.index')->with('success', 'Customer removed from DND list successfully.');
    }
}
