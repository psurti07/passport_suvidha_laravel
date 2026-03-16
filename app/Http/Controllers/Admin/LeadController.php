<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Exports\LeadsExport;       
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Maatwebsite\Excel\Excel as ExcelConstant; 
use Barryvdh\DomPDF\Facade\Pdf;

class LeadController extends Controller
{

    public function normalLeads(Request $request)
    {
        $from_date = $request->from_date ?? now()->subDay()->format('Y-m-d');
        $to_date   = $request->to_date ?? now()->format('Y-m-d');
        $search    = $request->search ?? '';

        $sort_by = $request->get('sort_by', 'id');
        $sort_direction = $request->get('sort_direction', 'desc');
        $per_page = $request->get('per_page', 10);

        $query = Customer::where('passport_type', 'normal')
            ->where('is_paid', 0)
            ->whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('mobile_number', 'like', "%$search%");
            });
        }

        // If export requested
        if ($request->type) {

            $leads = $query->get();
            $type = $request->type;

            switch ($type) {

                case 'excel':
                    return ExcelFacade::download(new LeadsExport($leads), 'leads.xlsx', ExcelConstant::XLSX);

                case 'csv':
                    return ExcelFacade::download(new LeadsExport($leads), 'leads.csv', ExcelConstant::CSV);

                case 'pdf':
                    $pdf = Pdf::loadView('exports.leads', compact('leads'));
                    return $pdf->download('leads.pdf');
            }
        }

        // Otherwise show normal leads list
        $leads = $query
            ->orderBy($sort_by, $sort_direction)
            ->paginate($per_page)
            ->appends($request->all());

        return view('admin.leads.normal', compact(
            'leads',
            'from_date',
            'to_date',
            'search'
        ));
    }


    public function show(Customer $customer)
    {
        return view('admin.leads.show', compact('customer'));
    }
}