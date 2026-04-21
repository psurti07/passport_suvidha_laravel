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
            'first_name',
            'last_name',
            'mobile_number',
            'is_dnd',
            'created_at'
        ])->where('is_dnd', 1);
        
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

        if($request->service) {
            $query->where('service_id', $request->service);
        }
        
        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('service_name', function ($row) {
                return $row->service ? $row->service->service_name : 'N/A';
            })

            ->addColumn('customer_name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i:s');
            })

            ->filterColumn('service_name', function($query, $keyword) {
                $query->whereHas('service', function($q) use ($keyword) {
                    $q->where('service_name', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('customer_name', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('actions', function ($row) {
                return '
                    <!-- Delete -->
                    <form action="'.route('admin.dnd.destroy', $row->id).'" method="POST" class="inline">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="button" 
                            onclick="confirmDelete(\''.$row->first_name.' customer\', this.form)"
                            class="text-red-600 hover:text-red-900" 
                            title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
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

                // Skip header
                if ($rowNumber == 1) {
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